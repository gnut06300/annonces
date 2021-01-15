<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
            $adminUser->setFirstName("Gerald")
                ->setLastName("COL")
                ->setEmail("gnut@gnut.eu")
                ->setPicture("https://via.placeholder.com/64")
                ->setIntroduction("Je suis dieu")
                ->setDescription("C'est moi le chef")
                ->setSlug("gerald-col")
                ->setHash($this->passwordEncoder->encodePassword(
            $adminUser,
            'password'
            ))
                ->addUserRole($adminRole);

                $manager->persist($adminUser);


        for ($k=1; $k <=5; $k++) { 
            # code...
            $user = new User();
            $user->setFirstName("prenom$k")
                ->setLastName("Nom$k")
                ->setEmail("test$k@test.fr")
                ->setPicture("https://via.placeholder.com/64")
                ->setIntroduction("Introduction_$k")
                ->setDescription("Description_$k")
                ->setSlug("prenom$k-Nom$k");

            $user->setHash($this->passwordEncoder->encodePassword(
            $user,
            'password'
            ));


                $manager->persist($user);
                $manager->flush();

                $slug2=$user->getSlug()."_".$user->getId();
                $user->setSlug($slug2);

                $manager->persist($user);

        

        for($i=0;$i<mt_rand(1,4);$i++){
        	
        	$slugify = new Slugify();
        	$title="Titre de l'annonce n°: $i";
        	$slug=$slugify->slugify($title);

        	$ad= new Ad();
        	$ad->setTitle("Titre de l'annonce n°: $i")
            	->setSlug($slug)
            	->setCoverImage("https://via.placeholder.com/350")
                ->setAuthor($user)
            	->setIntroduction("C'est une introduction de <strong>l'annonce n°: $i</strong>")
            	->setContent("Je suis le contenu de <strong>l'annonce n°: $i</strong>")
            	->setPrice(mt_rand(40,200))
            	->setRooms(mt_rand(1,5));

            for ($j=0; $j < mt_rand(2,5) ; $j++) { 
                # code...
                $image = new Image();
                $image->setUrl("https://via.placeholder.com/350")
                        ->setCaption("légende de l'image $j")
                        ->setAd($ad);

                $manager->persist($image);
            }

        	$manager->persist($ad);
        	$manager->flush();
        	//dump($ad->getId());
        	$slug2=$ad->getSlug()."_".$ad->getId();
        	$ad->setSlug($slug2);

        	$manager->persist($ad);
        	$manager->flush();
        }
        }
        //$manager->flush();
    }
}
