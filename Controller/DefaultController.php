<?php

namespace Comparon\SchedulingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ComparonSchedulingBundle:Default:index.html.twig');
    }
}
