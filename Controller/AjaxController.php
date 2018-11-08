<?php

namespace Emmedy\H5PBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/h5p/ajax")
 */
class AjaxController extends Controller
{
    /**
     * Callback that lists all h5p libraries.
     *
     * @Route("/libraries/")
     */
    public function librariesCallback(Request $request)
    {
        if ($request->get('machineName')) {
            return $this->libraryCallback($request);
        }
        $editor = $this->get('emmedy_h5p.editor');
        $editor->ajax->action(\H5PEditorEndpoints::LIBRARIES);
        exit();
    }

    /**
     * Callback that returns the content type cache
     *
     * @Route("/content-type-cache/")
     */
    public function contentTypeCacheCallback()
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
    public function libraryInstallCallback(Request $request)
    {
        $editor = $this->get('emmedy_h5p.editor');
        $editor->ajax->action(\H5PEditorEndpoints::LIBRARY_INSTALL, $request->get('token', 1), $request->get('id'));
        exit();
    }

    /**
     * @param Request $request
     *
     * @Route("/library-upload/")
     */
    public function libraryUploadCallback(Request $request)
    {
        $editor = $this->get('emmedy_h5p.editor');

        $token = $request->get('token');
        $filePath = $request->files->get('h5p')->getPathname();
        $contentId = $request->get('contentId');

        $editor->ajax->action(\H5PEditorEndpoints::LIBRARY_UPLOAD, $token, $filePath, $contentId);
        exit();
    }

    /**
     * Callback that returns data for a given library
     *
     * @param string $machine_name Machine name of library
     * @param int $major_version Major version of library
     * @param int $minor_version Minor version of library
     */
    private function libraryCallback(Request $request)
    {
        $editor = $this->get('emmedy_h5p.editor');
        $editor->ajax->action(\H5PEditorEndpoints::SINGLE_LIBRARY, $request->get('machineName'),
            $request->get('majorVersion'), $request->get('minorVersion'), $request->getLocale(), $this->get('emmedy_h5p.options')->getOption('storage_dir')
        );
        exit();
    }

    /**
     * Callback for file uploads.
     *
     * @param string $token Security token
     * @param int $content_id Content id
     * @Route("/files/")
     */
    function filesCallback(Request $request)
    {
        $editor = $this->get('emmedy_h5p.editor');
        $editor->ajax->action(\H5PEditorEndpoints::FILES, $request->get('token', 1), $request->get('id'));
        exit();
    }

}
