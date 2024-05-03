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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomeq', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Nom'],
            ])
            ->add('desceq', TextareaType::class, [ // Utilisation de TextareaType pour la description
                'label' => 'Description',
                'attr' => ['placeholder' => 'Description'],
            ])
            ->add('doceq', TextareaType::class, [ // Utilisation de TextareaType pour le document
                'label' => 'Documentation',
                'attr' => ['placeholder' => 'Documentation'],
            ])
            ->add('imageeq', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner une image.',
                    ]),
                    new Assert\Callback(function ($value, ExecutionContextInterface $context) {
                        if ($value === null || $value === '') {
                            // Si le champ est vide, la contrainte NotBlank s'en chargera
                            return;
                        }
            
                        $extension = $value->guessExtension();
                        if (!in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
                            $context->buildViolation('Veuillez télécharger une image avec une extension valide (JPEG, JPG, PNG, GIF).')
                                ->addViolation();
                        }
                    }),
                ],
            ])
            ->add('categeq', ChoiceType::class, [
                'label' => 'Categorie',
                'placeholder' => 'Choisir une categorie',
                'choices'  => [
                    'Fitness' => 'Fitness',
                    'Musculation' => 'Musculation',
                    'Cardio-training' => 'Cardio-training',
                ],
            ])
            
            
            ->add('marqueeq', TextType::class, [
                'label' => 'Marque',
                'attr' => ['placeholder' => 'Marque'],
            ])
            ->add('matriculeeq', TextType::class, [
                'label' => 'Matricule',
                'attr' => ['placeholder' => 'Matricule'],
            ])
            ->add('datepremainte', DateType::class, [
                'label' => 'Date de précédente maintenance',
                // Autres options éventuelles
            ])
            ->add('datepromainte', DateType::class, [
                'label' => 'Date de prochaine maintenance',
                // Autres options éventuelles
            ])
           ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class,
        ]);
    }
}

