<?php

namespace Blend\EzMatrixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BlendEzMatrixBundle:Default:index.html.twig', array('name' => $name));
    }
}
