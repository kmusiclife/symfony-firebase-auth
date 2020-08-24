<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PageController extends AbstractController
{

    private $session, $user;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $this->user = $session->get('user');
    }
    /**
     * @Route("/tos", name="page_tos")
     */
    public function PageTos()
    {
        return $this->render('page/tos.html.twig', []);
    }
    /**
     * @Route("/privacy", name="page_privacy")
     */
    public function PagePrivacy()
    {
        return $this->render('page/privacy.html.twig', []);
    }
    

}
