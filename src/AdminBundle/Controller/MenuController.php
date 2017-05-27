<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MenuController extends Controller
{
    /**
     * @Route("/menu")
     * @Template()
     */
    public function indexAction()
    {
        return $this->render('@Admin/Menu/index.html.twig', [
            'name' => 'menu'
        ]);
    }
}
