<?php

namespace App\Form;

use App\Entity\Avisequipement;
use App\Entity\Equipement;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisequipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('commaeq', TextType::class, [
                'label' => '   ',
                
            ])
            //->add('likes')
            //->add('dislikes')
            ->add('idus', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id', // ou un autre champ pour l'étiquette
            ])/*
            ->add('ideq', EntityType::class, [
                'class' => Equipement::class,
                'choice_label' => 'idEq', // ou un autre champ pour l'étiquette
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avisequipement::class,
        ]);
    }
}
