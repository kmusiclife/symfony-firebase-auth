<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Form\ProfileFormType;
use App\Entity\Profile;
use Kreait\Firebase\Firestore;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{

    private $firestore;

    public function __construct(SessionInterface $session, Firestore $firestore)
    {
        $this->firestore = $firestore;
    }

    /**
     * @Route("/", name="user_profile")
     */
    public function Profile(Request $request)
    {

        $database = $this->firestore->database();
        $docRef = $database->collection('users')->document( $this->getUser()->getFirebaseUid() );
        $fire_data = $docRef->snapshot()->data();

        $profile = new Profile();
        if($fire_data){
            $profile->setNameSei( $fire_data['name_sei'] );
            $profile->setNameMei( $fire_data['name_mei'] );
            $profile->setZip( $fire_data['zip'] );
            $profile->setTel( $fire_data['tel'] );
            $profile->setPref( $fire_data['pref'] );
            $profile->setAddr1( $fire_data['addr1'] );
            $profile->setAddr2( $fire_data['addr2'] );
            $profile->setAddr3( $fire_data['addr3'] );
        }
        $profile->setEmail( $this->getUser()->getUser()->email );
        $form = $this->createForm(ProfileFormType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $docRef = $database->collection('users')->document( $this->getUser()->getFirebaseUid() );
            $user_data = $docRef->snapshot()->data();
            $user_data['name_sei'] = $profile->getNameSei();
            $user_data['name_mei'] = $profile->getNameMei();
            $user_data['zip'] = $profile->getZip();
            $user_data['pref'] = $profile->getPref();
            $user_data['addr1'] = $profile->getAddr1();
            $user_data['addr2'] = $profile->getAddr2();
            $user_data['addr3'] = $profile->getAddr3();
            $user_data['tel'] = $profile->getTel();
            $docRef->set($user_data);

            $roles = $this->getUser()->getRoles();
            array_push($roles, 'ROLE_PROFILE');
            $this->getUser()->setRoles($roles);

            $em = $this->getDoctrine()->getManager();
            $em->persist( $this->getUser()->setData(json_encode($user_data)) );            
            $em->flush();

            $token = new UsernamePasswordToken($this->getUser(), null, 'main', $this->getUser()->getRoles());
            $this->get('security.token_storage')->setToken($token);

            $this->addFlash('success', 'プロフィールを変更しました');
            return $this->redirectToRoute('user_profile');


        }

        $profile_info = null;
        return $this->render('profile/index.html.twig', [
            'Profile_info' => $profile_info,
            'form' => $form->createView(),
        ]);
    }

}
