<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class IndexController extends AbstractController
{

    private $session;
    private $user;
    private $user_data;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $this->user = $this->session->get('user');
        $this->user_data = $this->session->get('user_data');
    }
    /**
     * @Route("/", name="index")
     */
    public function Index()
    {
        return $this->render('index/index.html.twig', [ 'user' => $this->user ]);
    }    

}
