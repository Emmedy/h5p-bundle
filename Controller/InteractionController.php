<?php

namespace Emmedy\H5PBundle\Controller;

use Emmedy\H5PBundle\Entity\Content;
use Emmedy\H5PBundle\Service\ResultService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/h5p/interaction")
 */
class InteractionController extends Controller
{
    /**
     * Access callback for the setFinished feature
     *
     * @Route("/set-finished/{token}")
     */
    public function setFinished(Request $request, $token)
    {
        if (!\H5PCore::validToken('result', $token)) {
            \H5PCore::ajaxError('Invalid security token');
        }

        /** @var ResultService $rs */
        $rs = $this->get('emmedy_h5p.result_storage');

        $result = $rs->handleRequest($request, $this->getUser()->getId());

        $em = $this->getDoctrine()->getManager();
        $em->persist($result);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    /**
     * Handles insert, updating and deleting content user data through AJAX.
     *
     * @Route("/content-user-data/{contentId}/{dataType}/{subContentId}")
     */
    public function contentUserData(Request $request, $contentId, $dataType, $subContentId)
    {
        return new JsonResponse();
    }

    /**
     * @Route("/embed/{content}")
     */
    public function embedAction(Request $request, Content $content)
    {
        return new JsonResponse();
    }
}