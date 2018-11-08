<?php

namespace Emmedy\H5PBundle\Core;

use Doctrine\ORM\EntityManager;
use Emmedy\H5PBundle\DependencyInjection\Configuration;
use Emmedy\H5PBundle\Editor\EditorStorage;
use Emmedy\H5PBundle\Entity\Content;
use Emmedy\H5PBundle\Entity\ContentLibraries;
use Emmedy\H5PBundle\Entity\LibrariesHubCache;
use Emmedy\H5PBundle\Entity\LibrariesLanguages;
use Emmedy\H5PBundle\Entity\Library;
use Emmedy\H5PBundle\Entity\LibraryLibraries;
use Emmedy\H5PBundle\Event\H5PEvents;
use Emmedy\H5PBundle\Event\LibrarySemanticsEvent;
use GuzzleHttp\Client;
use H5PPermission;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class H5PSymfony implements \H5PFrameworkInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var H5POptions
     */
    private $options;
    /**
     * @var EditorStorage
     */
    private $editorStorage;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var Router
     */
    private $router;

    /**
     * H5PSymfony constructor.
     * @param H5POptions $options
     * @param EditorStorage $editorStorage
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManager $manager
     * @param FlashBagInterface $flashBag
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param EventDispatcherInterface $eventDispatcher
     * @param Router $router
     */
    public function __construct(H5POptions $options,
                                EditorStorage $editorStorage,
                                TokenStorageInterface $tokenStorage,
                                EntityManager $manager,
                                FlashBagInterface $flashBag,
                                AuthorizationCheckerInterface $authorizationChecker,
                                EventDispatcherInterface $eventDispatcher,
                                Router $router)
    {
        $this->options = $options;
        $this->editorStorage = $editorStorage;
        $this->tokenStorage = $tokenStorage;
        $this->manager = $manager;
        $this->flashBag = $flashBag;
        $this->authorizationChecker = $authorizationChecker;
        $this->eventDispatcher = $eventDispatcher;
        $this->router = $router;
    }

    /**
     * Grabs the relative URL to H5P files folder.
     *
     * @return string
     */
    public function getRelativeH5PPath()
    {
        return $this->options->getRelativeH5PPath();
    }

    /**
     * Implements getPlatformInfo
     */
    public function getPlatformInfo()
    {
        return [
            'name' => 'symfony',
            'version' => Kernel::VERSION,
            'h5pVersion' => Configuration::H5P_VERSION,
        ];
    }

    /**
     * Implements fetchExternalData
     */
    public function fetchExternalData($url, $data = NULL, $blocking = TRUE, $stream = NULL)
    {

        $options = [];
        if (!empty($data)) {
            $options['headers'] = [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ];
            $options['form_params'] = $data;
        }

        if ($stream) {
            @set_time_limit(0);
        }

        try {
            $client = new Client();
            $response = $client->request(empty($data) ? 'GET' : 'POST', $url, $options);
            $response_data = (string)$response->getBody();
            if (empty($response_data)) {
                return FALSE;
            }

        } catch (\Exception $e) {
            $this->setErrorMessage($e->getMessage(), 'failed-fetching-external-data');
            return FALSE;
        }

        if ($stream && empty($response->error)) {
            // Create file from data
            $this->editorStorage->saveFileTemporarily($response_data);
            // TODO: Cannot rely on H5PEditor module – Perhaps we could use the
            // save_to/sink option to save directly to file when streaming ?
            // http://guzzle.readthedocs.io/en/latest/request-options.html#sink-option
            return TRUE;
        }

        return $response_data;
    }

    /**
     * Implements setLibraryTutorialUrl
     *
     * Set the tutorial URL for a library. All versions of the library is set
     *
     * @param string $machineName
     * @param string $tutorialUrl
     */
    public function setLibraryTutorialUrl($machineName, $tutorialUrl)
    {
        $libraries = $this->manager->getRepository('EmmedyH5PBundle:Library')->findBy(['machineName' => $machineName]);

        foreach ($libraries as $library) {
            $library->setTutorialUrl($tutorialUrl);
            $this->manager->persist($library);
        }
        $this->manager->flush();
    }

    /**
     * Keeps track of messages for the user.
     * @var array
     */
    private $messages = array('error' => array(), 'info' => array());

    /**
     * Implements setErrorMessage
     */
    public function setErrorMessage($message, $code = NULL)
    {
        $this->flashBag->add("error", "[$code]: $message");
    }

    /**
     * Implements setInfoMessage
     */
    public function setInfoMessage($message)
    {
        $this->flashBag->add("info", "$message");
    }

    /**
     * Implements getMessages
     */
    public function getMessages($type)
    {
        if (!$this->flashBag->has($type)) {
            return NULL;
        }
        $messages = $this->flashBag->get($type);
        return $messages;
    }

    /**
     * Implements t
     */
    public function t($message, $replacements = [])
    {
        foreach ($replacements as $search => $replace) {
            $message = str_replace($search, $replace, $message);
        }
        return $message;
    }

    /**
     * Implements getLibraryFileUrl
     */
    public function getLibraryFileUrl($libraryFolderName, $fileName)
    {
        return $this->options->getLibraryFileUrl($libraryFolderName, $fileName);
    }

    /**
     * Implements getUploadedH5PFolderPath
     */
    public function getUploadedH5pFolderPath($set = NULL)
    {
        return $this->options->getUploadedH5pFolderPath($set);
    }

    /**
     * Implements getUploadedH5PPath
     */
    public function getUploadedH5pPath($set = NULL)
    {
        return $this->options->getUploadedH5pPath($set);
    }

    /**
     * Implements loadLibraries
     */
    public function loadLibraries()
    {
        $res = $this->manager->getRepository('EmmedyH5PBundle:Library')->findBy([], ['title' => 'ASC', 'majorVersion' => 'ASC', 'minorVersion' => 'ASC']);

        $libraries = [];
        foreach ($res as $library) {
            $libraries[$library->getMachineName()][] = $library;
        }

        return $libraries;
    }

    /**
     * Implements getAdminUrl
     */
    public function getAdminUrl()
    {
        // Misplaced; not used by Core.
        $url = Url::fromUri('internal:/admin/content/h5p')->toString();
        return $url;
    }

    /**
     * Implements getLibraryId
     */
    public function getLibraryId($machineName, $majorVersion = NULL, $minorVersion = NULL)
    {
        $library = $this->manager->getRepository('EmmedyH5PBundle:Library')->findOneBy(['machineName' => $machineName, 'majorVersion' => $majorVersion, 'minorVersion' => $minorVersion]);

        return $library ? $library->getId() : null;
    }

    /**
     * Implements isPatchedLibrary
     */
    public function isPatchedLibrary($library)
    {
        if ($this->getOption('dev_mode', FALSE)) {
            return TRUE;
        }

        return $this->manager->getRepository('EmmedyH5PBundle:Library')->isPatched($library);
    }

    /**
     * Implements isInDevMode
     */
    public function isInDevMode()
    {
        $h5p_dev_mode = $this->getOption('dev_mode', FALSE);
        return (bool)$h5p_dev_mode;
    }

    /**
     * Implements mayUpdateLibraries
     */
    public function mayUpdateLibraries()
    {
        return $this->hasPermission(\H5PPermission::UPDATE_LIBRARIES);
    }

    /**
     * Implements getLibraryUsage
     *
     * Get number of content using a library, and the number of
     * dependencies to other libraries
     *
     * @param int $libraryId
     * @return array The array contains two elements, keyed by 'content' and 'libraries'.
     *               Each element contains a number
     */
    public function getLibraryUsage($libraryId, $skipContent = FALSE)
    {
        $usage = [];

        if ($skipContent) {
            $usage['content'] = -1;
        } else {
            $usage['content'] = $this->manager->getRepository('EmmedyH5PBundle:Library')->countContentLibrary($libraryId);
        }

        $usage['libraries'] = $this->manager->getRepository('EmmedyH5PBundle:LibraryLibraries')->countLibraries($libraryId);

        return $usage;
    }

    /**
     * Implements getLibraryContentCount
     *
     * Get a key value list of library version and count of content created
     * using that library.
     *
     * @return array
     *  Array containing library, major and minor version - content count
     *  e.g. "H5P.CoursePresentation 1.6" => "14"
     */
    public function getLibraryContentCount()
    {
        $contentCount = [];

        $results = $this->manager->getRepository('EmmedyH5PBundle:Content')->libraryContentCount();

        // Format results
        foreach ($results as $library) {
            $contentCount[$library['machineName'] . " " . $library['majorVersion'] . "." . $library['minorVersion']] = $library[1];
        }

        return $contentCount;
    }

    /**
     * Implements getLibraryStats
     */
    public function getLibraryStats($type)
    {
        $count = [];

        $results = $this->manager->getRepository('EmmedyH5PBundle:Counters')->findBy(['type' => $type]);

        // Extract results
        foreach ($results as $library) {
            $count[$library->getLibraryName() . " " . $library->getLibraryVersion()] = $library->getNum();
        }

        return $count;
    }

    /**
     * Implements getNumAuthors
     */
    public function getNumAuthors()
    {

        $contents = $this->manager->getRepository('EmmedyH5PBundle:Content')->countContent();

        // Return 1 if there is content and 0 if there is none
        return !$contents;
    }

    /**
     * Implements saveLibraryData
     *
     * @param array $libraryData
     * @param boolean $new
     */
    public function saveLibraryData(&$libraryData, $new = TRUE)
    {
        $preloadedJs = $this->pathsToCsv($libraryData, 'preloadedJs');
        $preloadedCss = $this->pathsToCsv($libraryData, 'preloadedCss');
        $dropLibraryCss = '';

        if (isset($libraryData['dropLibraryCss'])) {
            $libs = array();
            foreach ($libraryData['dropLibraryCss'] as $lib) {
                $libs[] = $lib['machineName'];
            }
            $dropLibraryCss = implode(', ', $libs);
        }

        $embedTypes = '';
        if (isset($libraryData['embedTypes'])) {
            $embedTypes = implode(', ', $libraryData['embedTypes']);
        }
        if (!isset($libraryData['semantics'])) {
            $libraryData['semantics'] = '';
        }
        if (!isset($libraryData['fullscreen'])) {
            $libraryData['fullscreen'] = 0;
        }
        if (!isset($libraryData['hasIcon'])) {
            $libraryData['hasIcon'] = 0;
        }
        if ($new) {
            $library = new Library();
            $library->setTitle($libraryData['title']);
            $library->setMachineName($libraryData['machineName']);
            $library->setMajorVersion($libraryData['majorVersion']);
            $library->setMinorVersion($libraryData['minorVersion']);
            $library->setPatchVersion($libraryData['patchVersion']);
            $library->setRunnable($libraryData['runnable']);
            $library->setFullscreen($libraryData['fullscreen']);
            $library->setEmbedTypes($embedTypes);
            $library->setPreloadedJs($preloadedJs);
            $library->setPreloadedCss($preloadedCss);
            $library->setDropLibraryCss($dropLibraryCss);
            $library->setSemantics($libraryData['semantics']);
            $library->setHasIcon($libraryData['hasIcon']);

            $this->manager->persist($library);
            $this->manager->flush();

            $libraryData['libraryId'] = $library->getId();
            if ($libraryData['runnable']) {
                $h5p_first_runnable_saved = $this->getOption('first_runnable_saved', FALSE);
                if (!$h5p_first_runnable_saved) {
                    $this->setOption('first_runnable_saved', 1);
                }
            }
        } else {
            $library = $this->manager->getRepository('EmmedyH5PBundle:Library')->find($libraryData['libraryId']);

            $library->setTitle($libraryData['title']);
            $library->setPatchVersion($libraryData['patchVersion']);
            $library->setFullscreen($libraryData['fullscreen']);
            $library->setEmbedTypes($embedTypes);
            $library->setPreloadedJs($preloadedJs);
            $library->setPreloadedCss($preloadedCss);
            $library->setDropLibraryCss($dropLibraryCss);
            $library->setSemantics($libraryData['semantics']);
            $library->setHasIcon($libraryData['hasIcon']);

            $this->manager->persist($library);
            $this->manager->flush();

            $this->deleteLibraryDependencies($libraryData['libraryId']);
        }

        $languages = $this->manager->getRepository('EmmedyH5PBundle:LibrariesLanguages')->findBy(['library' => $library]);
        foreach ($languages as $language) {
            $this->manager->remove($language);
        }
        $this->manager->flush();

        if (isset($libraryData['language'])) {
            foreach ($libraryData['language'] as $languageCode => $languageJson) {
                $language = new LibrariesLanguages();
                $language->setLibrary($library);
                $language->setLanguageCode($languageCode);
                $language->setLanguageJson($languageJson);

                $this->manager->persist($language);
            }
        }
        $this->manager->flush();
    }

    /**
     * Convert list of file paths to csv
     *
     * @param array $libraryData
     *  Library data as found in library.json files
     * @param string $key
     *  Key that should be found in $libraryData
     * @return string
     *  file paths separated by ', '
     */
    private function pathsToCsv($libraryData, $key)
    {
        if (isset($libraryData[$key])) {
            $paths = array();
            foreach ($libraryData[$key] as $file) {
                $paths[] = $file['path'];
            }
            return implode(', ', $paths);
        }
        return '';
    }

    public function lockDependencyStorage()
    {
    }

    public function unlockDependencyStorage()
    {
    }

    /**
     * Implements deleteLibraryDependencies
     */
    public function deleteLibraryDependencies($libraryId)
    {
        $libraries = $this->manager->getRepository('EmmedyH5PBundle:LibraryLibraries')->findBy(['library' => $libraryId]);
        foreach ($libraries as $library) {
            $this->manager->remove($library);
        }
        $this->manager->flush();
    }

    /**
     * Implements deleteLibrary. Will delete a library's data both in the database and file system
     */
    public function deleteLibrary($libraryId)
    {
        $library = $this->manager->getRepository('EmmedyH5PBundle:Library')->find($libraryId);
        $this->manager->remove($library);
        $this->manager->flush();

        // Delete files
        \H5PCore::deleteFileTree($this->getRelativeH5PPath() . "/libraries/{$library->getMachineName()}-{$library->getMajorVersion()}.{$library->getMinorVersion()}");
    }

    /**
     * Implements saveLibraryDependencies
     */
    public function saveLibraryDependencies($libraryId, $dependencies, $dependencyType)
    {
        foreach ($dependencies as $dependency) {
            $library = $this->manager->getRepository('EmmedyH5PBundle:Library')->find($libraryId);
            $requiredLibrary = $this->manager->getRepository('EmmedyH5PBundle:Library')->findOneBy(['machineName' => $dependency['machineName'], 'majorVersion' => $dependency['majorVersion'], 'minorVersion' => $dependency['minorVersion']]);
            $libraryLibraries = new LibraryLibraries();
            $libraryLibraries->setLibrary($library);
            $libraryLibraries->setRequiredLibrary($requiredLibrary);
            $libraryLibraries->setDependencyType($dependencyType);
            $this->manager->persist($libraryLibraries);
        }
        $this->manager->flush();
    }

    /**
     * Implements updateContent
     */
    public function updateContent($contentData, $contentMainId = NULL)
    {
        $content = $this->manager->getRepository('EmmedyH5PBundle:Content')->find($contentData['id']);
        return $this->storeContent($contentData, $content);
    }

    /**
     * Implements insertContent
     */
    public function insertContent($contentData, $contentMainId = NULL)
    {
        $content = new Content();
        return $this->storeContent($contentData, $content);
    }

    private function storeContent($contentData, Content $content)
    {
        $library = $this->manager->getRepository('EmmedyH5PBundle:Library')->find($contentData['library']['libraryId']);
        $content->setLibrary($library);
        $content->setParameters($contentData['params']);
        $content->setDisabledFeatures($contentData['disable']);
        $content->setFilteredParameters(null);

        $this->manager->persist($content);
        $this->manager->flush();

        return $content->getId();
    }

    /**
     * Implements resetContentUserData
     */
    public function resetContentUserData($contentId)
    {
        $contentUserDatas = $this->manager->getRepository('EmmedyH5PBundle:ContentUserData')->findBy(['mainContent' => $contentId, 'deleteOnContentChange' => true]);
        foreach ($contentUserDatas as $contentUserData) {
            $contentUserData->setData('RESET');
            $contentUserData->setTimestamp(time());

            $this->manager->persist($contentUserData);
        }
        $this->manager->flush();
    }

    /**
     * Implements getWhitelist
     */
    public function getWhitelist($isLibrary, $defaultContentWhitelist, $defaultLibraryWhitelist)
    {
        // Misplaced; should be done by Core.
        $h5p_whitelist = $this->getOption('whitelist', $defaultContentWhitelist);
        $whitelist = $h5p_whitelist;
        if ($isLibrary) {
            $h5p_library_whitelist_extras = $this->getOption('library_whitelist_extras', $defaultLibraryWhitelist);
            $whitelist .= ' ' . $h5p_library_whitelist_extras;
        }
        return $whitelist;

    }

    /**
     * Implements copyLibraryUsage
     */
    public function copyLibraryUsage($contentId, $copyFromId, $contentMainId = NULL)
    {
        $contentLibrariesFrom = $this->manager->getRepository('EmmedyH5PBundle:ContentLibraries')->findBy(['content' => $copyFromId]);
        $contentTo = $this->manager->getRepository('EmmedyH5PBundle:Content')->find($contentId);

        foreach ($contentLibrariesFrom as $contentLibrary) {
            $contentLibraryTo = clone $contentLibrary;
            $contentLibraryTo->setContent($contentTo);
            $this->manager->persist($contentLibraryTo);
        }
        $this->manager->flush();
    }

    /**
     * Implements deleteContentData
     */
    public function deleteContentData($contentId)
    {
        $content = $this->manager->getRepository('EmmedyH5PBundle:Content')->find($contentId);
        if ($content) {
            $this->manager->remove($content);
            $this->manager->flush();
        }
    }

    /**
     * Implements deleteLibraryUsage
     */
    public function deleteLibraryUsage($contentId)
    {
        $contentLibraries = $this->manager->getRepository('EmmedyH5PBundle:ContentLibraries')->findBy(['content' => $contentId]);
        foreach ($contentLibraries as $contentLibrary) {
            $this->manager->remove($contentLibrary);
        }
        $this->manager->flush();
    }

    /**
     * Implements saveLibraryUsage
     */
    public function saveLibraryUsage($contentId, $librariesInUse)
    {
        $content = $this->manager->getRepository('EmmedyH5PBundle:Content')->find($contentId);
        $dropLibraryCssList = array();
        foreach ($librariesInUse as $dependency) {
            if (!empty($dependency['library']['dropLibraryCss'])) {
                $dropLibraryCssList = array_merge($dropLibraryCssList, explode(', ', $dependency['library']['dropLibraryCss']));
            }
        }
        foreach ($librariesInUse as $dependency) {
            $dropCss = in_array($dependency['library']['machineName'], $dropLibraryCssList);
            $contentLibrary = new ContentLibraries();
            $contentLibrary->setContent($content);
            $library = $this->manager->getRepository('EmmedyH5PBundle:Library')->find($dependency['library']['libraryId']);
            $contentLibrary->setLibrary($library);
            $contentLibrary->setWeight($dependency['weight']);
            $contentLibrary->setDropCss($dropCss);
            $contentLibrary->setDependencyType($dependency['type']);
            $this->manager->persist($contentLibrary);
        }
        $this->manager->flush();
    }

    /**
     * Implements loadLibrary
     */
    public function loadLibrary($machineName, $majorVersion, $minorVersion)
    {
        $library = $this->manager->getRepository('EmmedyH5PBundle:Library')->findOneArrayBy(['machineName' => $machineName, 'majorVersion' => $majorVersion, 'minorVersion' => $minorVersion]);
        if (!$library) {
            return false;
        }
        $library['libraryId'] = $library['id'];

        $libraryLibraries = $this->manager->getRepository('EmmedyH5PBundle:LibraryLibraries')->findBy(['library' => $library['id']]);
        foreach ($libraryLibraries as $dependency) {
            $requiredLibrary = $dependency->getRequiredLibrary();
            $library["{$dependency->getDependencyType()}Dependencies"][] = [
                'machineName' => $requiredLibrary->getMachineName(),
                'majorVersion' => $requiredLibrary->getMajorVersion(),
                'minorVersion' => $requiredLibrary->getMinorVersion(),
            ];
        }

        return $library;
    }

    /**
     * Implements loadLibrarySemantics().
     */
    public function loadLibrarySemantics($machineName, $majorVersion, $minorVersion)
    {
        $library = $this->manager->getRepository('EmmedyH5PBundle:Library')->findOneBy(['machineName' => $machineName, 'majorVersion' => $majorVersion, 'minorVersion' => $minorVersion]);

        if ($library) {
            return $library->getSemantics();
        }
        return null;
    }

    /**
     * Implements alterLibrarySemantics().
     */
    public function alterLibrarySemantics(&$semantics, $name, $majorVersion, $minorVersion)
    {
        $this->eventDispatcher->dispatch(H5PEvents::SEMANTICS, new LibrarySemanticsEvent($semantics, $name, $majorVersion, $minorVersion));
    }

    /**
     * Implements loadContent().
     */
    public function loadContent($id)
    {

        // Not sure if we really need this since the content is loaded when the
        // content entity is loaded.
    }


    /**
     * Implements loadContentDependencies().
     */
    public function loadContentDependencies($id, $type = NULL)
    {
        $query = ['content' => $id];
        if ($type !== NULL) {
            $query['dependencyType'] = $type;
        }
        $contentLibraries = $this->manager->getRepository('EmmedyH5PBundle:ContentLibraries')->findBy($query, ['weight' => 'ASC']);
        $dependencies = [];
        foreach ($contentLibraries as $contentLibrary) {
            /** @var Library $library */
            $library = $contentLibrary->getLibrary();
            $dependencies[] = ['libraryId' => $library->getId(), 'machineName' => $library->getMachineName(), 'majorVersion' => $library->getMajorVersion(), 'minorVersion' => $library->getMinorVersion(),
                'patchVersion' => $library->getPatchVersion(), 'preloadedCss' => $library->getPreloadedCss(), 'preloadedJs' => $library->getPreloadedJs(), 'dropCss' => $contentLibrary->isDropCss(), 'dependencyType' => $contentLibrary->getDependencyType()];
        }

        return $dependencies;
    }

    public function getOption($name, $default = NULL)
    {
        return $this->options->getOption($name, $default);
    }

    public function setOption($name, $value)
    {
        $this->options->setOption($name, $value);
    }

    /**
     * Implements updateContentFields().
     */
    public function updateContentFields($id, $fields)
    {
        if (!isset($fields['filtered'])) {
            return;
        }

        $content = $this->manager->getRepository('EmmedyH5PBundle:Content')->find($id);
        $content->setFilteredParameters($fields['filtered']);
        $this->manager->persist($content);
        $this->manager->flush();
    }

    /**
     * Will clear filtered params for all the content that uses the specified
     * library. This means that the content dependencies will have to be rebuilt,
     * and the parameters refiltered.
     *
     * @param int $library_id
     */
    public function clearFilteredParameters($library_id)
    {

        $contents = $this->manager->getRepository('EmmedyH5PBundle:Content')->findBy(['library' => $library_id]);
        foreach ($contents as $content) {
            $content->setFilteredParameters('');
            $this->manager->persist($content);
        }
        $this->manager->flush();


//    // Clear hook_library_info_build() to use updated libraries
//    \Drupal::service('library.discovery.collector')->clear();
//
//    // Delete ALL cached JS and CSS files
//    \Drupal::service('asset.js.collection_optimizer')->deleteAll();
//    \Drupal::service('asset.css.collection_optimizer')->deleteAll();
//
//    // Reset cache buster
//    _drupal_flush_css_js();
//
//    // Clear field view cache for ALL H5P content
//    \Drupal\Core\Cache\Cache::invalidateTags(['h5p_content']);
    }

    /**
     * Get number of contents that has to get their content dependencies rebuilt
     * and parameters refiltered.
     *
     * @return int
     */
    public function getNumNotFiltered()
    {
        return $this->manager->getRepository('EmmedyH5PBundle:Content')->countNotFiltered();
    }

    /**
     * Implements getNumContent.
     */
    public function getNumContent($library_id)
    {
        return $this->manager->getRepository('EmmedyH5PBundle:Content')->countLibraryContent($library_id);
    }

    /**
     * Implements isContentSlugAvailable
     */
    public function isContentSlugAvailable($slug)
    {
        throw new \Exception();
//    return !db_query('SELECT slug FROM {h5p_content} WHERE slug = :slug', [':slug' => $slug])->fetchField();
    }

    /**
     * Implements saveCachedAssets
     */
    public function saveCachedAssets($key, $libraries)
    {
    }

    /**
     * Implements deleteCachedAssets
     */
    public function deleteCachedAssets($library_id)
    {
    }

    /**
     * Implements afterExportCreated
     */
    public function afterExportCreated($content, $filename)
    {
    }

    /**
     * Implements hasPermission
     *
     * @param int $permission
     * @param int $content_id
     * @return bool
     */
    public function hasPermission($permission, $content_id = NULL)
    {
        if (!$this->options->getOption('use_permission')) return true;

        switch ($permission) {
            case \H5PPermission::DOWNLOAD_H5P:
                return $content_id !== NULL && $this->authorizationChecker->isGranted('ROLE_H5P_DOWNLOAD_ALL');
            case \H5PPermission::EMBED_H5P:
                return $content_id !== NULL && $this->authorizationChecker->isGranted('ROLE_H5P_EMBED_ALL');
            case \H5PPermission::CREATE_RESTRICTED:
                return $this->authorizationChecker->isGranted('ROLE_H5P_CREATE_RESTRICTED_CONTENT_TYPES');
            case \H5PPermission::UPDATE_LIBRARIES:
                return $this->authorizationChecker->isGranted('ROLE_H5P_UPDATE_LIBRARIES');
            case \H5PPermission::INSTALL_RECOMMENDED:
                return $this->authorizationChecker->isGranted('ROLE_H5P_INSTALL_RECOMMENDED_LIBRARIES');
        }
        return FALSE;
    }

    /**
     * Replaces existing content type cache with the one passed in
     *
     * @param object $contentTypeCache Json with an array called 'libraries'
     *  containing the new content type cache that should replace the old one.
     */
    public function replaceContentTypeCache($contentTypeCache)
    {
        $this->truncateTable(LibrariesHubCache::class);

        foreach ($contentTypeCache->contentTypes as $ct) {
            $created_at = new \DateTime($ct->createdAt);
            $updated_at = new \DateTime($ct->updatedAt);

            $cache = new LibrariesHubCache();
            $cache->setMachineName($ct->id);
            $cache->setMajorVersion($ct->version->major);
            $cache->setMinorVersion($ct->version->minor);
            $cache->setPatchVersion($ct->version->patch);
            $cache->setH5pMajorVersion($ct->coreApiVersionNeeded->major);
            $cache->setH5pMinorVersion($ct->coreApiVersionNeeded->minor);
            $cache->setTitle($ct->title);
            $cache->setSummary($ct->summary);
            $cache->setDescription($ct->description);
            $cache->setIcon($ct->icon);
            $cache->setCreatedAt($created_at->getTimestamp());
            $cache->setUpdatedAt($updated_at->getTimestamp());
            $cache->setIsRecommended($ct->isRecommended);
            $cache->setPopularity($ct->popularity);
            $cache->setScreenshots(json_encode($ct->screenshots));
            $cache->setLicense(json_encode(isset($ct->license) ? $ct->license : []));
            $cache->setExample($ct->example);
            $cache->setTutorial(isset($ct->tutorial) ? $ct->tutorial : '');
            $cache->setKeywords(json_encode(isset($ct->keywords) ? $ct->keywords : []));
            $cache->setCategories(json_encode(isset($ct->categories) ? $ct->categories : []));
            $cache->setOwner($ct->owner);

            $this->manager->persist($cache);
        }
        $this->manager->flush();
    }

    private function truncateTable($tableClassName)
    {
        $cmd = $this->manager->getClassMetadata($tableClassName);
        $connection = $this->manager->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
        $connection->executeUpdate($q);
        $connection->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
