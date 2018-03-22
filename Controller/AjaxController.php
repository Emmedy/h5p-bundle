<?php

namespace Emmedy\H5PBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ajax")
 */
class AjaxController extends Controller
{
    /**
     * Callback that lists all h5p libraries.
     *
     * @Route("/libraries/")
     */
    function librariesCallback()
    {
        $editor = $this->get('emmedy_h5p.editor');
        $editor->ajax->action(\H5PEditorEndpoints::LIBRARIES);
        exit();
    }

    /**
     * Callback that returns the content type cache
     *
     * @Route("/content-type-cache/")
     */
    function contentTypeCacheCallback()
    {
        $editor = $this->get('emmedy_h5p.editor');
        $editor->ajax->action(\H5PEditorEndpoints::CONTENT_TYPE_CACHE);
        exit();
    }

    /**
     * Callback Install library from external file
     *
     * @param string $token Security token
     * @param int $content_id Id of content
     * @param string $machine_name Machine name of library
     *
     * @Route("/library-install/")
     */
    function libraryInstallCallback(Request $request) {
        $editor = $this->get('emmedy_h5p.editor');
        $editor->ajax->action(\H5PEditorEndpoints::LIBRARY_INSTALL, $request->get('token', 1), $request->get('id'));
        exit();
    }
}
