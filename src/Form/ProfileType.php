<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('mail')
            ->add('nom')
            ->add('prenom')
            ->add('tel')
            ->add('date_naissance', DateType::class, [
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('poids', RangeType::class, ['label' => 'Poids', 'attr' => ['min' => 0, 'max' => 200, 'step' => 1]])
            ->add('taille', RangeType::class, ['label' => 'Taille', 'attr' => ['min' => 0, 'max' => 200, 'step' => 1]])
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Modifier']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
