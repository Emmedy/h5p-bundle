<?php

namespace Emmedy\H5PBundle\Editor;

use Doctrine\ORM\EntityManager;
use Emmedy\H5PBundle\Core\H5PSymfony;

class Utilities
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * Utilities constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
   * Get editor settings needed for JS front-end
   *
   * @return array Settings needed for view
   */
  public static function getEditorSettings() {
    $contentValidator = H5PSymfony::getInstance('contentvalidator');
    $h5p_module_rel      = base_path() . drupal_get_path('module', 'h5p');

    $settings = [
      'filesPath'          => base_path() . H5PSymfony::getRelativeH5PPath(),
      'fileIcon'           => [
        'path'   => "{$h5p_module_rel}/vendor/h5p/h5p-editor/images/binary-file.png",
        'width'  => 50,
        'height' => 50,
      ],
      'ajaxPath'           => str_replace('%3A', ':', self::getAjaxPath()),
      'libraryPath'        => "{$h5p_module_rel}/vendor/h5p/h5p-editor/",
      'copyrightSemantics' => $contentValidator->getCopyrightSemantics(),
      'assets'             => self::getEditorAssets(),
      'apiVersion'         => \H5PCore::$coreApi,
    ];

    return $settings;
  }

  /**
   * Get assets needed to display editor. These are fetched from core.
   *
   * @return array Js and css for showing the editor
   */
  private static function getEditorAssets() {
    $h5p_module_rel = base_path() . drupal_get_path('module', 'h5p');
    $corePath   = "{$h5p_module_rel}/vendor/h5p/h5p-core/";
    $editorPath = "{$h5p_module_rel}/vendor/h5p/h5p-editor/";

    $css  = array_merge(
      self::getAssets(\H5PCore::$styles, $corePath),
      self::getAssets(\H5PEditor::$styles, $editorPath)
    );
    $js   = array_merge(
      self::getAssets(\H5PCore::$scripts, $corePath),
      self::getAssets(\H5PEditor::$scripts, $editorPath, ['scripts/h5peditor-editor.js'])
    );
    $js[] = self::getTranslationFilePath();

    return ['css' => $css, 'js' => $js];
  }

  /**
   * Extracts assets from a collection of assets
   *
   * @param array $collection Collection of assets
   * @param string $prefix Prefix needed for constructing the file-path of the assets
   * @param null|array $exceptions Exceptions from the collection that should be skipped
   *
   * @return array Extracted assets from the source collection
   */
  private static function getAssets($collection, $prefix, $exceptions = NULL) {
    $assets      = [];
    $cacheBuster = self::getCacheBuster();

    foreach ($collection as $item) {
      // Skip exceptions
      if ($exceptions && in_array($item, $exceptions)) {
        continue;
      }
      $assets[] = "{$prefix}{$item}{$cacheBuster}";
    }
    return $assets;
  }

  /**
   * Get cache buster
   *
   * @return string A cache buster that may be applied to resources
   */
  private static function getCacheBuster() {
    $cache_buster = \Drupal::state()->get('system.css_js_query_string', '0');
    return $cache_buster ? "?{$cache_buster}" : '';
  }

  /**
   * Translation file path for the editor. Defaults to English if chosen
   * language is not available.
   *
   * @return string Path to translation file for editor
   */
  private static function getTranslationFilePath() {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $h5p_module_rel = drupal_get_path('module', 'h5p');
    $languageFolder = "{$h5p_module_rel}/vendor/h5p/h5p-editor/language";
    $defaultLanguage = "{$languageFolder}/en.js";
    $chosenLanguage = "{$languageFolder}/{$language}.js";
    $cacheBuster = self::getCacheBuster();

    return base_path() . (file_exists($chosenLanguage) ? $chosenLanguage : $defaultLanguage) . $cacheBuster;
  }

  /**
   * Create URI for ajax the client may send to the server
   *
   * @return \Drupal\Core\GeneratedUrl|string Uri for AJAX
   */
  private static function getAjaxPath() {
    $securityToken = \H5PCore::createToken('editorajax');
    return Url::fromUri(
      "internal:/h5peditor/{$securityToken}/:contentId/"
    )->toString();
  }

  /**
   * Extract library information from library string
   *
   * @param string $library Library string with versioning, e.g. H5P.MultiChoice 1.9
   * @return array|bool
   */
  public static function getLibraryProperties($library)
  {
    $matches = [];
    preg_match_all('/(.+)\s(\d+)\.(\d+)$/', $library, $matches);
    if (count($matches) == 4) {
      $libraryData = [
        'name'         => $matches[1][0],
        'machineName'  => $matches[1][0],
        'majorVersion' => $matches[2][0],
        'minorVersion' => $matches[3][0],
      ];
      return $libraryData;
    }
    return false;
  }
}
