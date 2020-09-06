<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class IndexController extends AbstractController
{

    /**
     * @Route("/", name="index")
     */
    public function Index()
    {
        return $this->render('index/index.html.twig', []);
    }

}
