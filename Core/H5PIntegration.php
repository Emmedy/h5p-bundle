<?php

namespace Emmedy\H5PBundle\Core;


use Doctrine\ORM\EntityManager;
use Emmedy\H5PBundle\Entity\Content;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class H5PIntegration
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var \H5PCore
     */
    private $core;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var H5POptions
     */
    private $options;

    /**
     * H5PContent constructor.
     * @param \H5PCore $core
     * @param H5POptions $options
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManager $entityManager
     * @param RouterInterface $router
     */
    public function __construct(\H5PCore $core, H5POptions $options, TokenStorageInterface $tokenStorage, EntityManager $entityManager, RouterInterface $router)
    {
        $this->core = $core;
        $this->options = $options;
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * Prepares the generic H5PIntegration settings
     */
    public function getGenericH5PIntegrationSettings() {
        static $settings;

        if (!empty($settings)) {
            return $settings; // Only needs to be generated the first time
        }

        // Load current user
        $user = $this->tokenStorage->getToken()->getUser();

        // Load configuration settings
        $h5p_save_content_state = $this->options->getOption('save_content_state', false);
        $h5p_save_content_frequency = $this->options->getOption('save_content_frequency', 30);
        $h5p_hub_is_enabled = $this->options->getOption('hub_is_enabled', true);

        // Create AJAX URLs
        $set_finished_url = "";//Url::fromUri('internal:/h5p-ajax/set-finished.json', ['query' => ['token' => \H5PCore::createToken('result')]])->toString(TRUE)->getGeneratedUrl();
        $content_user_data_url = "";//Url::fromUri('internal:/h5p-ajax/content-user-data/:contentId/:dataType/:subContentId', ['query' => ['token' => \H5PCore::createToken('contentuserdata')]])->toString(TRUE)->getGeneratedUrl();

        // Define the generic H5PIntegration settings
        $settings = array(
            'baseUrl' => "/",
            'url' => $this->options->getRelativeH5PPath(),
            'postUserStatistics' => is_object($user) ? $user->getId() : null,
            'ajax' => array(
                'setFinished' => $set_finished_url,
                'contentUserData' => $content_user_data_url,
            ),
            'saveFreq' => $h5p_save_content_state ? $h5p_save_content_frequency : false,
            'l10n' => array(
                'H5P' => $this->core->getLocalization(),
            ),
            'hubIsEnabled' => $h5p_hub_is_enabled,
            'siteUrl' => $this->router->getContext()->getBaseUrl()
        );

        if (is_object($user)) {
            $settings['user'] = [
                'name' => $user->getUsername(),
                'mail' => $user->getEmail(),
            ];
        }

        return $settings;
    }

    public function getH5PContentIntegrationSettings(Content $content)
    {
        $content_user_data = [
            0 => [
                'state' => '{}',
            ]
        ];
        if (is_object($this->tokenStorage->getToken()->getUser())) {
            $contentUserData = $this->entityManager->getRepository('EmmedyH5PBundle:ContentUserData')->findOneBy(['mainContent' => $content, 'user' => $this->tokenStorage->getToken()->getUser()]);
            $content_user_data[$contentUserData->getSubContentId()][$contentUserData->getDataId()] = $contentUserData->getData();
        }

        $filteredParameters = $this->getFilteredParameters($content);

        $embedUrl = $this->router->generate('emmedy_h5p_h5p_embed', ['content' => $content->getId()]);
        $resizerUrl = $this->router->getContext()->getBaseUrl() . $this->options->getH5PAssetPath() . '/h5p-core/js/h5p-resizer.js';
        $displayOptions = $this->core->getDisplayOptionsForView($content->getDisabledFeatures(), $content->getId());

        return array(
            'library' => (string)$content->getLibrary(),
            'jsonContent' => $filteredParameters,
            'fullScreen' => $content->getLibrary()->isFullscreen(),
            'exportUrl' => $this->getExportURL($content),
            'embedCode' => '<iframe src="' . $embedUrl . '" width=":w" height=":h" frameborder="0" allowfullscreen="allowfullscreen"></iframe>',
            'resizeCode' => '<script src="' . $resizerUrl . '" charset="UTF-8"></script>',
            'url' => $embedUrl,
            'title' => 'Not Available',
            'contentUserData' => $content_user_data,
            'displayOptions' => $displayOptions,
        );
    }

    public function getFilteredParameters(Content $content) {
        $contentData = [
            'title' => 'Interactive Content',
            'id' => $content->getId(),
            'slug' => 'interactive-content',
            'library' => [
                'name' => $content->getLibrary()->getMachineName(),
                'majorVersion' => $content->getLibrary()->getMajorVersion(),
                'minorVersion' => $content->getLibrary()->getMinorVersion(),
            ],
            'params' => $content->getParameters(),
            'filtered' => $content->getFilteredParameters(),
            'embedType' => 'div',
        ];

        return $this->core->filterParameters($contentData);
    }

    protected function getExportURL(Content $content) {
        if ($this->options->getOption('export', true)) {
            return $this->options->getRelativeH5PPath()."/exports/interactive-content-" . $content->getId() . '.h5p';
        } else {
            return '';
        }

    }
}