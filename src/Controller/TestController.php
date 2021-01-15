<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index()
    {
    	$a='Gégé';
        dump($this);
    	$tab=['eric'=>52,'Gérald'=>53,'florian'=>26];
    	/*dump($tab);
    	dump($a);*/
    	return $this->render('test/index.html.twig', [
            'controller_name' => 'HomeController',
            'prenom'=>$a,
            'tableau'=>$tab,
            'age'=>16,
        ]);
    }
}

