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
        
        // verify
        // https://firebase-php.readthedocs.io/en/latest/authentication.html#verify-a-firebase-id-token

        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
        } catch (\InvalidArgumentException $e) {
            $res = new Response( json_encode(array( 'error' => $e->getMessage() )) );
            $res->setStatusCode(500);
            $res->headers->set('Content-Type','application/json');
            return $res;

        } catch (InvalidToken $e) {
            $res = new Response( json_encode(array( 'error' => $e->getMessage() )) );
            $res->setStatusCode(500);
            $res->headers->set('Content-Type','application/json');
            return $res;
        }

        $uid = $verifiedIdToken->getClaim('sub');
        $user = $this->auth->getUser($uid);
        $this->session->set('user', $user);

        $json = json_encode(array( 'status' => $user ? true : false ));
        $res =  new Response($json);
        $res->setStatusCode(200);
        $res->headers->set('Content-Type','application/json');
        return $res;
    }
    /**
     * @Route("/signin", name="signin")
     */
    public function signin()
    {
        return $this->render('sign/signin.html.twig', []);
    }
    /**
     * @Route("/signout", name="signout")
     */
    public function signout()
    {
        return $this->render('sign/signout.html.twig', []);
    }

}
