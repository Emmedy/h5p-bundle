<?php

namespace Emmedy\H5PBundle\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EditController extends Controller
{
    /**
     * @Route("h5p/edit")
     */
    public function editAction()
    {
        return $this->render('@EmmedyH5P/edit.html.twig');
    }
}