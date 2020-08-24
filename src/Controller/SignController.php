<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Kreait\Firebase\Auth;

class SignController extends AbstractController
{

    private $session, $auth;

    public function __construct(SessionInterface $session, Auth $auth)
    {
        $this->session = $session;
        $this->auth = $auth;
    }
    /**
     * @Route("/verify", name="verify")
     */
    public function verify(Request $request)
    {   
        $idToken = $request->headers->get('idToken');
        $firebase_admin_path = file_get_contents($this->getParameter('kernel.project_dir').'/.firebase-admin.json');
        $firebase_admin = json_decode($firebase_admin_path);
        
        // verify
        // https://firebase-php.readthedocs.io/en/latest/authentication.html#verify-a-firebase-id-token

        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
        } catch (\InvalidArgumentException $e) {
            echo 'The token could not be parsed: '.$e->getMessage();
        } catch (InvalidToken $e) {
            echo 'The token is invalid: '.$e->getMessage();
        }
        $uid = $verifiedIdToken->getClaim('sub');
        $user = $this->auth->getUser($uid);
        $this->session->set('user', $user);

        $json = json_encode(array( 'status' => $user ? true : false ));
        $res =  new Response($json);
        $res->headers->set('Content-Type','application/json');

        return $res;
    }
    /**
     * @Route("/signin", name="signin")
     */
    public function signin()
    {
        return $this->render('sign/signin.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    /**
     * @Route("/signout", name="signout")
     */
    public function signout()
    {
        $this->session->set('user', null);
        return $this->render('sign/signout.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

}
