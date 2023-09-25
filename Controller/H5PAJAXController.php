<?php

namespace Emmedy\H5PBundle\Controller;

use Exception;
use Emmedy\H5PBundle\Core\H5POptions;
use Emmedy\H5PBundle\Event\H5PEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/h5p/ajax")
 */
class H5PAJAXController extends AbstractController
{
    protected \H5peditor $h5peditor;
    protected H5POptions $h5POptions;

    public function __construct(\H5peditor $h5peditor, H5POptions $h5poption)
    {
        $this->h5peditor = $h5peditor;
        $this->h5POptions = $h5poption;
    }

    /**
     * Callback that lists all h5p libraries.
     *
     * @Route("/libraries/")
     * @param Request $request
     * @return string
     */
    public function librariesCallback(Request $request)
    {
        ob_start();

        if ($request->get('machineName')) {
            return $this->libraryCallback($request);
        }
        //get editor
        $editor = $this->h5peditor;
        $editor->ajax->action(\H5PEditorEndpoints::LIBRARIES);

        $output = ob_get_contents();
        ob_end_clean();

        return $this->json(json_decode($output, true));
    }

    /**
     * Callback that returns the content type cache
     *
     * @Route("/content-type-cache/")
     */
    public function contentTypeCacheCallback()
    {
        ob_start();

        $editor = $this->h5peditor;
        $editor->ajax->action(\H5PEditorEndpoints::CONTENT_TYPE_CACHE);

        $output = ob_get_contents();
        ob_end_clean();

        return $this->json(json_decode($output, true));
    }

    /**
     * Callback for translations
     *
     * @param Request $request
     * @Route("/translations/")
     *
     * @return JsonResponse
     */
    public function TranslationsCallback(Request $request)
    {
        ob_start();

        $editor = $this->h5peditor;
        $language = $request->get('language');
        $editor->ajax->action(
            \H5PEditorEndpoints::TRANSLATIONS,
            $language
        );

        $output = ob_get_contents();
        ob_end_clean();

        return $this->json(json_decode($output, true));
    }

    /**
     * Callback Install library from external file
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/library-install/")
     */
    public function libraryInstallCallback(Request $request)
    {
        ob_start();

        $editor = $this->h5peditor;
        $editor->ajax->action(
            \H5PEditorEndpoints::LIBRARY_INSTALL,
            $request->get('token', 1),
            $request->get('id')
        );

        $output = ob_get_contents();
        ob_end_clean();

        return $this->json(json_decode($output, true));
    }

    /**
     * Callback that returns data for a given library
     *
     * @param Request $request
     * @return JsonResponse
     */
    private function libraryCallback(Request $request)
    {
        ob_start();

        //$machineName, $majorVersion, $minorVersion, $languageCode, $prefix = '', $fileDir = '', $defaultLanguage
        $editor = $this->h5peditor;
        $locale = $request->getLocale() != null ? $request->getLocale() : 'en';
        $editor->ajax->action(
            \H5PEditorEndpoints::SINGLE_LIBRARY,
            $request->get('machineName'),
            $request->get('majorVersion'),
            $request->get('minorVersion'),
            $locale,
            $this->h5POptions->getOption('storage_dir'),
            '',
            $locale
        );
        /*new H5PEvents('library', NULL, NULL, NULL,
            $request->get('machineName'), $request->get('majorVersion') . '.' . $request->get('minorVersion'),
             $this->getUser() != null ? $this->getUser()->getId() : 0, $this->getDoctrine()->getManager()
        );*/

        $output = ob_get_contents();
        ob_end_clean();

        return $this->json(json_decode($output, true));
    }

    /**
     * Callback for uploading a library
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     * @Route("/library-upload/")
     */
    public function libraryUploadCallback(Request $request)
    {
        ob_start();

        $editor = $this->h5peditor;
        $filePath = null;
        if (isset($_FILES['h5p'])) {
            $filePath = $_FILES['h5p']['tmp_name'];
        } else {
            //generate error
            throw new Exception('POST file is missing');
        }

        $editor->ajax->action(
            \H5PEditorEndpoints::LIBRARY_UPLOAD,
            $request->get('token', 1),
            $filePath,
            $request->get('contentId')
        );

        $output = ob_get_contents();
        ob_end_clean();

        return $this->json(json_decode($output, true));
    }

    /**
     * Callback for file uploads.
     *
     * @param Request $request
     * @return JsonResponse
     * @Route("/files/")
     */
    public function filesCallback(Request $request)
    {
        ob_start();

        $editor = $this->h5peditor;
        $id = $request->get('id') != null ? $request->get('id') : $request->get('contentId');
        $editor->ajax->action(
            \H5PEditorEndpoints::FILES,
            $request->get('token', 1),
            $id
        );

        $output = ob_get_contents();
        ob_end_clean();

        return $this->json(json_decode($output, true));
    }

    /**
     * Callback for filtering.
     *
     * @param Request $request
     * @return JsonResponse
     * @Route("/filter/")
     */
    public function filterCallback(Request $request)
    {
        ob_start();

        $editor = $this->h5peditor;
        $editor->ajax->action(
            \H5PEditorEndpoints::FILTER,
            $request->get('token', 1),
            $request->get('libraryParameters')
        );

        $output = ob_get_contents();
        ob_end_clean();

        return $this->json(json_decode($output, true));
    }
}
