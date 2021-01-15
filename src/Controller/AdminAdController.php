<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page}", name="admin_ads_index", requirements={"page"="[0-9]{1,}"})
     */
    public function index(AdRepository $repo,$page=1)
    {
        $limit = 5 ;//on veut 5 enregistrement par pages

        $start = $limit * $page - $limit; //calcul de l'offset(le début)
        $total = count($repo->findAll());

        $pages = ceil($total/$limit);// nbrs de page total arondi à l'entier supérieur
    	$ads=$repo->findBy([],[],$limit,$start);
        return $this->render('admin/ad/index.html.twig', [
        'ads'=>$ads, 
        'page'=>$page,
        'pages'=>$pages
        ]);
    }
}
