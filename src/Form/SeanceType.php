<?php

namespace App\Form;

use App\Entity\Seance;
use Symfony\Component\DomCrawler\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class SeanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        $builder
        ->add('nom', ChoiceType::class, [
            'choices' => [
                'Crossfit' => 'Crossfit',
                'BodyPump' => 'BodyPump',
                'BodyAttack' => 'BodyAttack',
                'Boxe' => 'Boxe',
                'Yoga' => 'Yoga',
                'Spinning' => 'Spinning',


                // Ajoutez d'autres options ici
            ],
        ])
                 
        ->add('horaire')

        
        ->add('jourseance', ChoiceType::class, [
            'choices' => [
                'Lundi' => 'Lundi',
                'Mardi' => 'Mardi',
                'Mercredi' => 'Mercredi',
                'jeudi' => 'Jeudi',
                'Vendredi' => 'Vendredi',
                'Samedi' => 'Samedi',
                'Dimanche' => 'Dimanche',
            ],
        ])
            ->add('numesalle')
            ->add('duree')
            ->add('imageseance', FileType::class, [
                'label' => 'Image de la séance',
                'required' => true, // Ou true si vous le souhaitez
                'mapped' => false, // Ne pas mapper la propriété dans l'entité pour l'édition
                'attr' => ['class' => 'form-control'],

            ]);
            
        ;
       // $builder->get('imageseance')->addModelTransformer(new StringToFileTransformer());

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seance::class,
        ]);
    }
}
