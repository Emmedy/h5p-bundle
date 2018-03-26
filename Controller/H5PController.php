<?php

namespace Emmedy\H5PBundle\Controller;


use Emmedy\H5PBundle\Editor\Utilities;
use Emmedy\H5PBundle\Entity\Content;
use Emmedy\H5PBundle\Form\Type\H5pType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class H5PController extends Controller
{
    /**
     * @Route("h5p/list")
     */
    public function listAction()
    {
        $contents = $this->getDoctrine()->getRepository('EmmedyH5PBundle:Content')->findAll();
        return $this->render('@EmmedyH5P/list.html.twig', ['contents' => $contents]);
    }

    /**
     * @Route("h5p/show/{content}")
     */
    public function showAction(Content $content)
    {
        $h5pIntegration = $this->get('emmedy_h5p.integration')->getGenericH5PIntegrationSettings();
        $contentIdStr = 'cid-' . $content->getId();
        $h5pIntegration['contents'][$contentIdStr] = $this->get('emmedy_h5p.integration')->getH5PContentIntegrationSettings($content);

        $preloaded_dependencies = $this->get('emmedy_h5p.core')->loadContentDependencies($content->getId(), 'preloaded');

        $files = $this->get('emmedy_h5p.core')->getDependenciesFiles($preloaded_dependencies, $this->get('emmedy_h5p.options')->getRelativeH5PPath());

        if ($content->getLibrary()->isFrame()) {
            $jsFilePaths = array_map(function($asset){ return $asset->path; }, $files['scripts']);
            $cssFilePaths = array_map(function($asset){ return $asset->path; }, $files['styles']);
            $coreAssets = $this->get('emmedy_h5p.integration')->getCoreAssets();

            $h5pIntegration['core']['scripts'] = $coreAssets['scripts'];
            $h5pIntegration['core']['styles'] = $coreAssets['styles'];
            $h5pIntegration['contents'][$contentIdStr]['scripts'] = $jsFilePaths;
            $h5pIntegration['contents'][$contentIdStr]['styles'] = $cssFilePaths;
        }

        return $this->render('@EmmedyH5P/show.html.twig', ['contentId' => $content->getId(), 'isFrame' => $content->getLibrary()->isFrame(), 'h5pIntegration' => $h5pIntegration, 'files' => $files]);
    }

    /**
     * @Route("h5p/embed/{content}")
     */
    public function embedAction(Request $request, Content $content)
    {

    }

    /**
     * @Route("h5p/new")
     */
    public function newAction(Request $request)
    {
        return $this->handleRequest($request);
    }

    /**
     * @Route("h5p/edit/{content}")
     */
    public function editAction(Request $request, Content $content)
    {
        return $this->handleRequest($request, $content);
    }

    private function handleRequest(Request $request, Content $content = null)
    {
        $formData = null;
        if ($content) {
            $formData['parameters'] = $content->getParameters();
            $formData['library'] = $content->getLibrary()->getMachineName() . " " . $content->getLibrary()->getMajorVersion() . "." . $content->getLibrary()->getMinorVersion();
        }
        $form = $this->createForm(H5pType::class, $formData);
        $form->handleRequest($request);
        if ($form->isValid()) {

            $data = $form->getData();
            $contentId = $this->storeLibraryData($data['library'], $data['parameters'], $content);

            return $this->redirectToRoute('emmedy_h5p_h5p_show', ['content' => $contentId]);
        }

        $h5pIntegration = $this->get('emmedy_h5p.integration')->getGenericH5PIntegrationSettings();
        $h5pIntegration['editor'] = $this->get('emmedy_h5p.integration')->getEditorIntegrationSettings($this->get('emmedy_h5p.contentvalidator'));
        if ($content) {
            $h5pIntegration['editor']['contentId'] = $content->getId();
        }

        return $this->render('@EmmedyH5P/edit.html.twig', ['form' => $form->createView(), 'h5pIntegration' => $h5pIntegration]);
    }

    private function storeLibraryData($library, $parameters, Content $content = null)
    {
        $libraryData = Utilities::getLibraryProperties($library);
        $libraryData['libraryId'] = $this->getDoctrine()->getRepository('EmmedyH5PBundle:Library')->findIdBy($libraryData['machineName'], $libraryData['majorVersion'], $libraryData['minorVersion']);

        $contentData = [
            'library' => $libraryData,
            'params' => $parameters,
            'disable' => 0
            ];
        if ($content) {
            $contentData['id'] = $content->getId();
        }
        $contentId = $this->get('emmedy_h5p.core')->saveContent($contentData);
        $this->updateLibraryFiles($contentId, $contentData, $content);

        return $contentId;
    }

    private function updateLibraryFiles($contentId, $contentData, Content $oldContent = null)
    {
        if ($oldContent) {
            $oldLibrary = [
                'name' => $oldContent->getLibrary()->getMachineName(),
                'machineName' => $oldContent->getLibrary()->getMachineName(),
                'majorVersion' => $oldContent->getLibrary()->getMajorVersion(),
                'minorVersion' => $oldContent->getLibrary()->getMinorVersion()
            ];
            $oldParameters = json_decode($oldContent->getParameters());
        } else {
            $oldLibrary = null;
            $oldParameters = null;
        }
        // Keep new files, delete files from old parameters
        $editor = $this->get('emmedy_h5p.editor');
        $editor->processParameters(
            $contentId,
            $contentData['library'],
            json_decode($contentData['params']),
            $oldLibrary,
            $oldParameters
        );
    }
}