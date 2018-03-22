<?php

namespace Emmedy\H5PBundle\Controller;


use Emmedy\H5PBundle\Editor\Utilities;
use Emmedy\H5PBundle\Entity\Content;
use Emmedy\H5PBundle\Form\Type\H5pType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EditController extends Controller
{
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
            $contentId = $this->storeLibraryData($data['library'], $data['parameters'], $content->getId());

            return $this->redirectToRoute('emmedy_h5p_edit_edit', ['content' => $contentId]);
        }

        return $this->render('@EmmedyH5P/edit.html.twig', ['form' => $form->createView(), 'contentId' => $content ? $content->getId() : null]);
    }

    private function storeLibraryData($library, $parameters, $contentId)
    {
        $libraryData = Utilities::getLibraryProperties($library);
        $libraryData['libraryId'] = $this->getDoctrine()->getRepository('EmmedyH5PBundle:Library')->findIdBy($libraryData['machineName'], $libraryData['majorVersion'], $libraryData['minorVersion']);

        $content = [
            'library' => $libraryData,
            'params' => $parameters,
            'disable' => 0
            ];
        if ($contentId) {
            $content['id'] = $contentId;
        }

        return $this->get('emmedy_h5p.core')->saveContent($content);
    }
}