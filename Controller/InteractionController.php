<?php

namespace Emmedy\H5PBundle\Controller;

use Emmedy\H5PBundle\Entity\Content;
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
        return new JsonResponse();
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