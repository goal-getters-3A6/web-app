<?php

namespace App\Form;

use App\Entity\Abonnement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class AbonnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('montantab', NumberType::class, [
            'label' => 'Montant',
            'attr' => ['class' => 'form-control']
        ])
        ->add('dateexpirationab', DateType::class, [
            'label' => 'Date d\'expiration',
            'widget' => 'single_text',
            'attr' => ['class' => 'form-control']
        ])
        ->add('codepromoab', TextType::class, [
            'label' => 'Code promo',
            'attr' => [ // Définir des attributs HTML personnalisés
                'class' => 'form-control', // Ajouter une classe CSS
                'placeholder' => 'GoFit30 par exemple', // Ajouter un placeholder
            ],
        ])
        ->add('typeab', TextType::class, [
            'label' => 'Type',
            'attr' => ['class' => 'form-control']
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonnement::class,
        ]);
    }
}
