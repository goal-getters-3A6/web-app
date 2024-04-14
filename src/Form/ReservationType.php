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
            
            
            ->add('iduser', EntityType::class, [
                'class' => 'App\Entity\User',
                'choice_label' => function ($user) {
                    return $user->getPrenom() . ' ' . $user->getNom(); // Affiche le prénom suivi du nom de l'utilisateur
                },
            ])
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
