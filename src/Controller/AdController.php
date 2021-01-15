<?php

namespace App\Controller;


use App\Entity\Ad;
use App\Entity\Image;
use App\Entity\ImageUpload;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use App\Services\ImagesUploadService;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
     	//dump($this);
     	//$repo=$this->getDoctrine()->getRepository(Ad::class);// on va cherche de repository de Ad
     	$ads=$repo->findAll();//prend tous les enregistrement de la table
     	//dump($ads);
        return $this->render('ad/index.html.twig', [
            'ads'=>$ads, //je transmet a twig une clé ads qui contient $ads
        ]);
    }
    /**
     * @Route("/ads/new", name="ads_create")
     * @IsGranted("ROLE_USER")
     */
        public function create(EntityManagerInterface $manager,Request $request,ImagesUploadService $upload)
    {
        $ad = new Ad();

        $ad->setAuthor($this->getUser());//recupére le user connecté
        /*
        $image= new Image();
        $image2= new Image();

        $image->setUrl('http')
                ->setCaption('description image 1');
        $ad->addImage($image);//et non l'inverse

        $image2->setUrl('http:')
                ->setCaption('description image 2');
        $ad->addImage($image2);
        */

        //dump($request);
        $form = $this->createForm(AnnonceType::class,$ad);
        //dump($ad);
        $form -> handleRequest($request);
        //dump($ad);
        if($form->isSubmitted() && $form->isValid()){
            $slugify = new Slugify();
            $slug=$slugify->slugify($ad->getTitle());
            //dump($slug);
            $ad->setSlug($slug);
            //dump($ad);exit;
            foreach ($ad->getImages() as $image) {
                # code...
                //dump($image);
                $image->setAd($ad);
                $manager->persist($image);
            }
            //inclusion de la fonction
            //gestion des images uploadées mis dans services 
            $upload->upload($ad,$manager);
            //exit;

            $manager->persist($ad);
            $manager->flush();// On l'enregistre pour récupérer id

            $slug2=$ad->getSlug()."_".$ad->getId();
            $ad->setSlug($slug2);

            

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success','L\'annonce de titre : '.$ad->getTitle().' est bien enregistrée');
            /*$this->addFlash('success','ca ne marche pas');
            $this->addFlash('danger','ceci est un test');
            $this->addFlash('info','ceci est un test');*/

            return $this->redirectToRoute('ads_show',['slug'=>$ad->getSlug()]);
        }

        return $this->render('ad/new.html.twig', [
          'form'=>$form->createView(),
        ]);

    }
    /**
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security ("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Cette annonce ne vous appartient pas.")
     */
    public function edit(EntityManagerInterface $manager,Request $request,Ad $ad,ImagesUploadService $upload)
    {
       

        //dump($request);
        $form = $this->createForm(AnnonceType::class,$ad);
        //dump($ad);
        $form -> handleRequest($request);
        //dump($ad);
        if($form->isSubmitted() && $form->isValid()){
            //gestion des images uploadées mis dans services 
            $upload->upload($ad,$manager);

            //suppresion de images uploadées
            $tabid = $ad->tableau_id;
            $tabid = preg_replace('#^,#','',$tabid);
            //dump($tabid);exit;
            $tabid = explode(",", $tabid);
            //dump($tabid);exit;
            foreach ($tabid as $id) {
                # code...
                foreach ($ad->getImageUploads() as $image) {
                    if ($id == $image->getId()){
                        $manager->remove($image);
                        $manager->flush();
                        unlink($_SERVER['DOCUMENT_ROOT'].$image->getUrl());
                    }
                //dump($image);
                # code...
                }
            }
            //dump($_SERVER);
            //dump($ad);exit;
            
            //exit;
            $slugify = new Slugify();
            $slug=$slugify->slugify($ad->getTitle());
            //dump($slug);
            $ad->setSlug($slug);
            //dump($ad->getImages());
            //dump($ad);exit;
            foreach ($ad->getImages() as $image) {
                # code...
                //dump($image);
                $image->setAd($ad);
                $manager->persist($image);
            }
            $manager->persist($ad);
            $manager->flush();// On l'enregistre pour récupérer id

            $slug2=$ad->getSlug()."_".$ad->getId();
            $ad->setSlug($slug2);

            

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success','L\'annonce de titre : '.$ad->getTitle().' a bien été modifiée');
            /*$this->addFlash('success','ca ne marche pas');
            $this->addFlash('danger','ceci est un test');
            $this->addFlash('info','ceci est un test');*/

            return $this->redirectToRoute('ads_show',['slug'=>$ad->getSlug()]);
        }

        return $this->render('ad/edit.html.twig', [
          'form'=>$form->createView(),
          'ad'=>$ad,
        ]);

    }
    /**
     * @Route("/ads/{slug}", name="ads_show")
     */
    /*public function show(AdRepository $repo,$slug)
    {
     	$ad=$repo->findOneBySlug($slug);
     	//dump($slug);*/
     	public function show(Ad $ad)
     {
     	//dump($ad);
        return $this->render('ad/show.html.twig', [
          'ad'=>$ad,  
        ]);
    }
    /**
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security ("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Vous ne pouvez pas supprimer cette annonce elle ne vous appartient pas.")
     */
      
        public function delete(Ad $ad,EntityManagerInterface $manager)
     {
        
        $manager->remove($ad);
        $manager->flush();
        $this->addFlash('success','L\'annonce de titre : '.$ad->getTitle().' a bien été supprimée');
        return $this->redirectToRoute('ads_index');


    }
    
}
