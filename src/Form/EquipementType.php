<?php

/*namespace App\Form;

use App\Entity\Equipement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomeq')
            ->add('desceq')
            ->add('doceq')
            ->add('imageeq')
            ->add('categeq')
            ->add('noteeq')
            ->add('marqueeq')
            ->add('matriculeeq')
            ->add('datepremainte')
            ->add('datepromainte')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class,
        ]);
    }
}*/


namespace App\Form;

use App\Entity\Equipement;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomeq', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Name'],
            ])
            ->add('desceq', TextareaType::class, [ // Utilisation de TextareaType pour la description
                'label' => 'Description',
                'attr' => ['placeholder' => 'Description'],
            ])
            ->add('doceq', TextareaType::class, [ // Utilisation de TextareaType pour le document
                'label' => 'Documentation',
                'attr' => ['placeholder' => 'Document'],
            ])
            ->add('imageeq', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
            ])
            ->add('categeq', ChoiceType::class, [
                'label' => 'Categorie',
                'placeholder' => 'Select a category',
                'choices'  => [
                    'Fitnesse' => 'Fitnesse',
                    'Musculation' => 'Musculation',
                    'Cardio-training' => 'Cardio-training',
                ],
            ])
            
            
            ->add('marqueeq', TextType::class, [
                'label' => 'Marque',
                'attr' => ['placeholder' => 'Brand'],
            ])
            ->add('matriculeeq', TextType::class, [
                'label' => 'Matricule',
                'attr' => ['placeholder' => 'Matriculation'],
            ])
            ->add('datepremainte')
            ->add('datepromainte')
           ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class,
        ]);
    }
}

