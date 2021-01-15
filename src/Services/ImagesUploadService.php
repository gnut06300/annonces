<?php 
namespace App\Services;

use App\Entity\ImageUpload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImagesUploadService extends AbstractController
{

	public function upload($ad,$manager){
		foreach ($ad->file as $file) {
                # code...
                //dump($file->getClientOriginalName());
                /*$position_point=strpos($file->getClientOriginalName(),'.');
                $original_name=substr($file->getClientOriginalName(),0,$position_point);
                dump($original_name);*/    //1er methode
                /*$original_name=preg_replace('#\.(jpg|png|gif)$#','',$file->getClientOriginalName());
                dump($original_name);*/    //2eme methode
                $original_name=preg_replace('#\.[a-zA-Z0-9]*$#','',$file->getClientOriginalName());
                //dump($original_name);   //3eme solutions
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $upload=new ImageUpload();
                $upload->setAd($ad);
                $upload->setName($original_name);
                $upload->setUrl('/uploads/'.$fileName);

                $manager->persist($upload);

                $file->move($this->getParameter('images_directory'),$fileName);
            }

	}


}
 ?>