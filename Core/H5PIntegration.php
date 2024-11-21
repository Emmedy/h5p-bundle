<?php


namespace Emmedy\H5PBundle\Core;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use H5PCore;
use H5peditor;
use Emmedy\H5PBundle\Entity\Content;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class H5PIntegration extends H5PUtils
{
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @var \H5PCore
     */
    private \H5PCore $core;

    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * @var H5POptions
     */
    private H5POptions $options;

    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * @var Packages
     */
    private Packages $assetsPaths;

    /**
     * @var \H5PContentValidator
     */
    private \H5PContentValidator $contentValidator;

    /**
     * H5PContent constructor.
     * @param \H5PCore $core
     * @param H5POptions $options
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @param Packages $packages
     * @param \H5PContentValidator $contentValidator
     */
    public function __construct(
        \H5PCore $core,
        H5POptions $options,
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        RequestStack $requestStack,
        Packages $packages,
        \H5PContentValidator $contentValidator
    ) {
        parent::__construct($tokenStorage);
        $this->core = $core;
        $this->options = $options;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->assetsPaths = $packages;
        $this->contentValidator = $contentValidator;
    }

    /**
     * Prepares the generic H5PIntegration settings.
     * @return array|null
     */
    public function getGenericH5PIntegrationSettings(): ?array
    {
        static $settings;
        if (!empty($settings)) {
            // Only needs to be generated the first time
            return $settings;
        }

        // Load current user
        $user = $this->getCurrentOrAnonymousUser();

        // Load configuration settings
        $saveContentState = $this->options->getOption('save_content_state', false);
        $saveContentFrequency = $this->options->getOption('save_content_frequency', 30);
        $hubIsEnabled = $this->options->getOption('hub_is_enabled', true);

        // Create AJAX URLs
        $setFinishedUrl = $this->router->generate('emmedy_h5p_h5pinteraction_setfinished', [
            'token' => \H5PCore::createToken('result')
        ]);
        $contentUserDataUrl = $this->router->generate('emmedy_h5p_h5pinteraction_contentuserdata', [
            'contentId' => ':contentId',
            'dataType' => ':dataType',
            'subContentId' => ':subContentId',
            'token' => \H5PCore::createToken('contentuserdata')
        ]);

        // Define the generic H5PIntegration settings
        $settings = [
            'baseUrl' => "/",
            'url' => $this->options->getRelativeH5PPath(),
            'postUserStatistics' => is_object($user),
            'ajax' => ['setFinished' => $setFinishedUrl, 'contentUserData' => $contentUserDataUrl],
            'saveFreq' => $saveContentState ? $saveContentFrequency : false,
            'l10n' => ['H5P' => $this->core->getLocalization()],
            'hubIsEnabled' => $hubIsEnabled,
            'siteUrl' => $this->requestStack->getMainRequest()->getUri(),
            'libraryConfig' => $this->core->h5pF->getLibraryConfig(),
            'reportingIsEnabled' => $this->core->h5pF->getOption('enable_lrs_content_type', false) === 1
        ];
        if (is_object($user)) {
            $settings['user'] = [
                'name' => method_exists($user, 'getUsername') ? $user->getUsername() : $user->getUserIdentifier(),
                'mail' => method_exists($user, 'getEmail') ?
                    $user->getEmail() :
                    $user->getUserIdentifier() . '@' . $_SERVER['HTTP_HOST'],
            ];
        }
        return $settings;
    }

    /**
     * Get a list with prepared asset links that is used when JS loads components.
     *
     * @param array|null $keys [$keys] Optional keys, first for JS second for CSS.
     * @return array
     */
    public function getCoreAssets(?array $keys = null): array
    {
        if (empty($keys)) {
            $keys = ['scripts', 'styles'];
        }
        // Prepare arrays
        $assets = [
            $keys[0] => [],
            $keys[1] => [],
        ];
        // Add all core scripts
        foreach (\H5PCore::$scripts as $script) {
            $assets[$keys[0]][] = "{$this->options->getH5PAssetPath()}/h5p-core/$script";
        }
        // and styles
        foreach (\H5PCore::$styles as $style) {
            $assets[$keys[1]][] = "{$this->options->getH5PAssetPath()}/h5p-core/$style";
        }
        return $assets;
    }

    public function getH5PContentIntegrationSettings(Content $content): array
    {
        $content_user_data = [
            0 => [
                'state' => '{}',
            ]
        ];
        if (is_object($this->getCurrentOrAnonymousUser())) {
            $contentUserData = $this->entityManager
                ->getRepository('Emmedy\H5PBundle\Entity\ContentUserData')
                ->findOneBy([
                    'mainContent' => $content,
                    'user' => $this->getUserId($this->getCurrentOrAnonymousUser())
                ]);
            if ($contentUserData) {
                $content_user_data[$contentUserData->getSubContentId()][$contentUserData->getDataId()] = $contentUserData->getData();
            }
        }
        $filteredParameters = $this->getFilteredParameters($content);
        $embedUrl = $this->router->generate('emmedy_h5p_h5pinteraction_embed', ['content' => $content->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $resizerUrl = $this->getH5PAssetUrl() . '/h5p-core/js/h5p-resizer.js';
        $displayOptions = $this->core->getDisplayOptionsForView($content->getDisabledFeatures(), $content->getId());
        return array(
            'library' => (string)$content->getLibrary(),
            'jsonContent' => $filteredParameters,
            'fullScreen' => $content->getLibrary()->isFullscreen(),
            'exportUrl' => $this->getExportUrl($content),
            'embedCode' => '<iframe src="' .
                $embedUrl .
                '" width=":w" height=":h" frameborder="0" allowfullscreen="allowfullscreen"></iframe>',
            'resizeCode' => '<script src="' . $resizerUrl . '" charset="UTF-8"></script>',
            'url' => $embedUrl,
            'title' => 'Not Available',
            'contentUserData' => $content_user_data,
            'displayOptions' => $displayOptions,
        );
    }

    public function getFilteredParameters(Content $content): string|object|null
    {
        $params = json_decode($content->getParameters());
        $contentData = [
            'title' => 'Interactive Content',
            'id' => $content->getId(),
            'slug' => 'interactive-content',
            'library' => [
                'name' => $content->getLibrary()->getMachineName(),
                'majorVersion' => $content->getLibrary()->getMajorVersion(),
                'minorVersion' => $content->getLibrary()->getMinorVersion(),
            ],
            'params' => json_encode($params->params ?? ''),
            'filtered' => $content->getFilteredParameters(),
            'embedType' => 'div',
        ];
        if (!empty($contentData['filtered'] && $contentData['filtered'] == '{}')) {
            $contentData['filtered'] = null;
        }

        return $this->core->filterParameters($contentData);
    }

    protected function getExportUrl(Content $content): string
    {
        if ($this->options->getOption('export', true)) {
            return $this->options->getRelativeH5PPath() . "/exports/interactive-content-" . $content->getId() . '.h5p';
        } else {
            return '';
        }
    }

    public function getEditorIntegrationSettings($contentId = null): array
    {
        $editorSettings = [
            'filesPath' => $this->options->getRelativeH5PPath(),
            'fileIcon' => [
                'path' => $this->getH5PAssetUrl() . "/h5p-editor/images/binary-file.png",
                'width' => 50,
                'height' => 50,
            ],
            'ajaxPath' => $this->router->getContext()->getBaseUrl() . "/h5p/ajax/",
            'libraryPath' => $this->getH5PAssetUrl() . "/h5p-editor/",
            'copyrightSemantics' => $this->contentValidator->getCopyrightSemantics(),
            'metadataSemantics' => $this->contentValidator->getMetadataSemantics(),
            'assets' => $this->getEditorAssets(),
            'apiVersion' => \H5PCore::$coreApi,
        ];
        if ($contentId) {
            $editorSettings['contentId'] = $contentId;
        }
        $settings = $this->getGenericH5PIntegrationSettings();
        $settings['editor'] = $editorSettings;
        return $settings;
    }

    /**
     * Get assets needed to display editor. These are fetched from core.
     *
     * @return array Js and css for showing the editor
     */
    private function getEditorAssets(): array
    {
        $h5pAssetUrl = $this->getH5PAssetUrl();
        $corePath = "{$h5pAssetUrl}/h5p-core/";
        $editorPath = "{$h5pAssetUrl}/h5p-editor/";
        $css = array_merge(
            $this->getAssets(H5PCore::$styles, $corePath),
            $this->getAssets(H5peditor::$styles, $editorPath)
        );
        $js = array_merge(
            $this->getAssets(H5PCore::$scripts, $corePath),
            $this->getAssets(H5PEditor::$scripts, $editorPath, ['scripts/h5peditor-editor.js'])
        );
        $js[] = $this->getTranslationFilePath();
        return ['css' => $css, 'js' => $js];
    }

    /**
     * Extracts assets from a collection of assets.
     *
     * @param array $collection Collection of assets
     * @param string $prefix Prefix needed for constructing the file-path of the assets
     * @param null|array $exceptions Exceptions from the collection that should be skipped
     *
     * @return array Extracted assets from the source collection
     */
    private function getAssets($collection, $prefix, $exceptions = null): array
    {
        $assets = [];
        $cacheBuster = $this->getCacheBuster();
        foreach ($collection as $item) {
            // Skip exceptions
            if ($exceptions && in_array($item, $exceptions)) {
                continue;
            }
            $assets[] = "$prefix$item$cacheBuster";
        }
        return $assets;
    }

    /**
     * Get cache buster.
     *
     * @return string A cache buster that may be applied to resources
     */
    public function getCacheBuster(): string
    {
        $cache_buster = \H5PCore::$coreApi['majorVersion'] . '.' . \H5PCore::$coreApi['minorVersion'];
        return $cache_buster ? "?=$cache_buster" : '';
    }

    /**
     * Translation file path for the editor. Defaults to English if chosen.
     * language is not available.
     *
     * @return string Path to translation file for editor
     */
    public function getTranslationFilePath(): string
    {
        $language = $this->requestStack->getCurrentRequest()->getLocale();
        $h5pAssetUrl = $this->getH5PAssetUrl();
        $languageFolder = "{$h5pAssetUrl}/h5p-editor/language";
        //check default language exist if exist load the file
        //if folder not exist load english in default
        $defaultLanguage = file_exists("{$languageFolder}/{$language}.js") ?
            "{$languageFolder}/{$language}.js" :
            "{$languageFolder}/en.js";
        $chosenLanguage = "{$languageFolder}/{$language}.js";
        $cacheBuster = $this->getCacheBuster();
        return (file_exists($this->options->getAbsoluteWebPath() . $chosenLanguage) ?
                $chosenLanguage :
                $defaultLanguage
            ) . $cacheBuster;
    }

    private function getH5PAssetUrl(): string
    {
        return $this->assetsPaths->getUrl($this->options->getH5PAssetPath());
    }

    /**
     * Access to direct access to the configuration to save time.
     * @return H5POptions
     */
    public function getOptions(): H5POptions
    {
        return $this->options;
    }
}
