<?php

namespace Emmedy\H5PBundle\Editor;


use Doctrine\ORM\EntityManager;
use Emmedy\H5PBundle\Entity\Content;

class LibraryStorage
{
    /**
     * @var \H5PCore
     */
    private $core;
    /**
     * @var \H5peditor
     */
    private $editor;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * LibraryStorage constructor.
     * @param \H5PCore $core
     * @param \H5peditor $editor
     * @param EntityManager $entityManager
     */
    public function __construct(\H5PCore $core, \H5peditor $editor, EntityManager $entityManager)
    {
        $this->core = $core;
        $this->editor = $editor;
        $this->entityManager = $entityManager;
    }

    public function storeLibraryData($library, $parameters, Content $content = null)
    {
        $libraryData = Utilities::getLibraryProperties($library);
        $libraryData['libraryId'] = $this->entityManager->getRepository('EmmedyH5PBundle:Library')->findIdBy($libraryData['machineName'], $libraryData['majorVersion'], $libraryData['minorVersion']);

        if ($content) {
            $oldLibrary = [
                'name' => $content->getLibrary()->getMachineName(),
                'machineName' => $content->getLibrary()->getMachineName(),
                'majorVersion' => $content->getLibrary()->getMajorVersion(),
                'minorVersion' => $content->getLibrary()->getMinorVersion()
            ];
            $oldParameters = json_decode($content->getParameters());
        } else {
            $oldLibrary = null;
            $oldParameters = null;
        }

        $contentData = [
            'library' => $libraryData,
            'params' => $parameters,
            'disable' => 0
        ];
        if ($content) {
            $contentData['id'] = $content->getId();
        }
        $contentId = $this->core->saveContent($contentData);
        $this->updateLibraryFiles($contentId, $contentData, $oldLibrary, $oldParameters);

        return $contentId;
    }

    private function updateLibraryFiles($contentId, $contentData, $oldLibrary, $oldParameters)
    {
        // Keep new files, delete files from old parameters
        $this->editor->processParameters(
            $contentId,
            $contentData['library'],
            json_decode($contentData['params']),
            $oldLibrary,
            $oldParameters
        );
    }
}