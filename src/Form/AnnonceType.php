<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tableau_id',HiddenType::class,['required'=>false])
            ->add('title',TextType::class,['required'=>True]) //option que l'on peut mettre
            //->add('slug') //on va le traité nous même dans AdController
            ->add('price',MoneyType::class)
            ->add('introduction')
            ->add('content')
            ->add('coverImage',UrlType::class)
            ->add('rooms')
            ->add('images',CollectionType::class,[
                'entry_type'=>ImageType::class, //ImageType est le fichier du formulaire Image
                'allow_add'=>true,
                'allow_delete'=>true,
            ])
            ->add('file',FileType::class,[
                'label'=>false,
                'required'=>false,
                'multiple'=>true,
                'attr'=>['placeholder'=>'choisir une image'],


            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
