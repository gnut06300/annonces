<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{

	/**
     * @Route("/register", name="account_register")
     */
    public function register(EntityManagerInterface $manager,Request $request,UserPasswordEncoderInterface $passwordEncoder) //attentation http fondation pour le F5 du request
    {
        
    	$user = new User();
        
        $form = $this->createForm(RegistrationType::class,$user);
        
        $form -> handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

        	//dump($user);
        	$password=$user->getHash();
        	$pass_encoded=$passwordEncoder->encodePassword($user,$password);
      		$user->setHash($pass_encoded);

      		$slugify = new Slugify();
            $slug=$slugify->slugify($user->getFirstName().'-'.$user->getLastName());
            $user->setSlug($slug);

            $manager->persist($user);
            $manager->flush();

            $slug2=$user->getSlug()."_".$user->getId();
            $user->setSlug($slug2);

            

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success','L\'utilisateur : '.$user->getFirstName().' a bien été crée');
            /*$this->addFlash('success','ca ne marche pas');
            $this->addFlash('danger','ceci est un test');
            $this->addFlash('info','ceci est un test');*/

            return $this->redirectToRoute('account_login');
        
        }
        
        return $this->render('account/register.html.twig', [
          'form'=>$form->createView(),
        ]);
    }
/**
     * @Route("/account/profile", name="account_profile")
     */
    public function profile(EntityManagerInterface $manager,Request $request) //attentation http fondation pour le F5 du request
    {
        $user = $this->getUser();
        
        $form = $this->createForm(AccountType::class,$user);
        
        $form -> handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success','Le profil : '.$user->getFirstName().' a bien été modifié');
            /*$this->addFlash('success','ca ne marche pas');
            $this->addFlash('danger','ceci est un test');
            $this->addFlash('info','ceci est un test');*/

            return $this->redirectToRoute('account_index');
        
        }
        
        return $this->render('account/profile.html.twig', [
          'form'=>$form->createView(),
          'user'=>$user,
        ]);


    }
     /**
     * @Route("/account/", name="account_index")
     */
    public function myAccount()
    {

        return $this->render('user/index.html.twig', [
          'user'=>$this->getUser(),
        ]);

    }

    /**
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
    	// get the login error if there is one
	    $error = $utils->getLastAuthenticationError();

        return $this->render('account/login.html.twig', [
            'controller_name' => 'AccountController',
            'loginError'=>$error,
        ]);
    }

    /**
     * @Route("/account/password-update", name="account_password")
     */
    public function upatePassword(EntityManagerInterface $manager,Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
      
        $user = $this->getUser();

        $passwordUpdate = new PasswordUpdate();
        
        $form = $this->createForm(PasswordUpdateType::class,$passwordUpdate);
        
        $form -> handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
              //dump($user);
           if (!password_verify($passwordUpdate->getOldPassword(),$user->getHash() )) {
             # code...
              $this->addFlash('danger','l\'ancien mot de passe est incorrect');
           }else{

              $newpassword=$passwordUpdate->getNewPassword();
              $pass_encoded=$passwordEncoder->encodePassword($user,$newpassword);
              $user->setHash($pass_encoded);

              $manager->persist($user);
              $manager->flush();

              $this->addFlash('success','Le nouveau password de : '.$user->getFirstName().' a bien été modifié');
            

              return $this->redirectToRoute('account_index');

           }


           
        
        }
        return $this->render('account/password.html.twig', [
           'form'=>$form->createView(), 
           'user'=>$user,
        ]);
    }

	/**
     * @Route("/deconnexion", name="account_logout")
     */
    public function logout()
    {
        
    }

}
