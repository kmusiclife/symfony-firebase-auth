<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Firestore;

class SignController extends AbstractController
{

    private $session, $auth, $firestore, $user;

    public function __construct(SessionInterface $session, Auth $auth, Firestore $firestore)
    {
        $this->session = $session;
        $this->user = $this->session->get('user');
        $this->auth = $auth;
        $this->firestore = $firestore;
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
        $this->user = $this->auth->getUser($uid);
        $this->session->set('user', $this->user);

        $database = $this->firestore->database();
        $docRef = $database->collection('users')->document( $this->user->uid );
        $user_data = $docRef->snapshot()->data();

        if(!$user_data) $user_data = array();
        $docRef->set($user_data);
        $this->session->set('user_data', $user_data);

        $redirect_url = $this->session->get('redirect_url');
        $json = json_encode(array( 'user' => $this->user, 'redirect_url' => $redirect_url ) );
        $this->session->set('redirect_url', null);

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
        $this->session->set('user', null);
        $this->session->set('user_data', null);
        return $this->render('sign/signout.html.twig', []);
    }

}
