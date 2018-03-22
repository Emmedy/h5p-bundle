<?php

namespace Emmedy\H5PBundle\Editor;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EditorAjax implements \H5PEditorAjaxInterface {
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * EditorAjax constructor.
     * @param EntityManager $manager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManager $manager, TokenStorageInterface $tokenStorage)
    {
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
    }


    /**
   * Gets latest library versions that exists locally
   *
   * @return array Latest version of all local libraries
   */
  public function getLatestLibraryVersions() {
//    $connection = \Drupal::database();
      return $this->manager->getRepository('EmmedyH5PBundle:Library')->findLatestLibraryVersions();

//    // Retrieve latest major version
//    $max_major_version = $connection->select('h5p_libraries', 'h1');
//    $max_major_version->fields('h1', ['machine_name']);
//    $max_major_version->addExpression('MAX(h1.major_version)', 'major_version');
//    $max_major_version->condition('h1.runnable', 1);
//    $max_major_version->groupBy('h1.machine_name');
//
//    // Find latest minor version among the latest major versions
//    $max_minor_version = $connection->select('h5p_libraries', 'h2');
//    $max_minor_version->fields('h2', [
//      'machine_name',
//      'major_version',
//    ]);
//    $max_minor_version->addExpression('MAX(h2.minor_version)', 'minor_version');
//
//    // Join max major version and minor versions
//    $max_minor_version->join($max_major_version, 'h1', "
//      h1.machine_name = h2.machine_name AND
//      h1.major_version = h2.major_version
//    ");
//
//    // Group together on major versions to get latest minor version
//    $max_minor_version->groupBy('h2.machine_name');
//    $max_minor_version->groupBy('h2.major_version');
//
//    // Find latest patch version from latest major and minor version
//    $latest = $connection->select('h5p_libraries', 'h3');
//    $latest->addField('h3', 'library_id', 'id');
//    $latest->fields('h3', [
//      'machine_name',
//      'title',
//      'major_version',
//      'minor_version',
//      'patch_version',
//      'has_icon',
//      'restricted',
//    ]);
//
//    // Join max minor versions with the latest patch version
//    $latest->join($max_minor_version, 'h4', "
//      h4.machine_name = h3.machine_name AND
//      h4.major_version = h3.major_version AND
//      h4.minor_version = h3.minor_version
//    ");
//
//    // Grab the results
//    $results = $latest->execute()->fetchAll();
//    return $results;
  }

  /**
   * Get locally stored Content Type Cache. If machine name is provided
   * it will only get the given content type from the cache
   *
   * @param $machineName
   *
   * @return array|object|null Returns results from querying the database
   */
  public function getContentTypeCache($machineName = NULL) {

      // Get only the specified content type from cache
      if ($machineName !== NULL) {
          $contentTypeCache = $this->manager->getRepository('EmmedyH5PBundle:LibrariesHubCache')->findOneBy(['machineName' => $machineName]);
          return [$contentTypeCache];
    }

    // Get all cached content types
    return $this->manager->getRepository('EmmedyH5PBundle:LibrariesHubCache')->findAll();
  }

  /**
   * Create a list of the recently used libraries
   *
   * @return array machine names. The first element in the array is the most
   * recently used.
   */
  public function getAuthorsRecentlyUsedLibraries()
  {
      $recentlyUsed = [];

      $user = $this->tokenStorage->getToken()->getUser();
      if (is_object($user)) {
          $events = $this->manager->getRepository('EmmedyH5PBundle:Event')->findRecentlyUsedLibraries($user);
          foreach ($events as $event) {
              $recentlyUsed[] = $event['libraryName'];
          }
      }

    return $recentlyUsed;
  }

  /**
   * Checks if the provided token is valid for this endpoint
   *
   * @param string $token The token that will be validated for.
   *
   * @return bool True if successful validation
   */
  public function validateEditorToken($token) {
//    return \H5PCore::validToken('editorajax', $token);
      return true;
  }
}
