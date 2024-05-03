<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
      
            ->add('ids', EntityType::class, [
                'class' => 'App\Entity\Seance',
                'choice_label' => function ($seance) {
                    return $seance->getNom() . ' - ' . $seance->getJourseance() . ' - Durée: ' . $seance->getDuree();
                },
            ]);
            
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
