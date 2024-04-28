<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('categorierec', ChoiceType::class, [
            'label' => 'Catégorie',
            'choices' => [
                'Qualité' => 'Qualité',
                'Problème Technique' => 'Problème Technique',
                'Communication' => 'Communication',
                'Durabilité' => 'Durabilité',
            ],
            'placeholder' => 'Sélectionnez une catégorie', // Ajouter le placeholder ici
            'attr' => ['class' => 'form-control'],
        ])
        ->add('descriptionrec', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'attr' => [
                'class' => 'form-control',
                'rows' => 5, // Définissez le nombre de lignes que vous voulez afficher
                'placeholder' => 'Entrez une description détaillée de votre réclamation ici...',
            ],
        ])

            ->add('piecejointerec', FileType::class, [
                'label' => 'Piece jointe',
                'required' => false, // Le champ n'est pas obligatoire
                'mapped' => true, // Ne pas mapper directement à l'entité
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier JPEG ou PNG',
                    ])
                ],
            ])
            ->add('oddrec', TextType::class, [
                'label' => 'ODD touché', // Définir le libellé du champ
                'required' => true, // Rendre le champ obligatoire si nécessaire
                'attr' => [ // Définir des attributs HTML personnalisés
                    'class' => 'form-control', // Ajouter une classe CSS
                    'placeholder' => 'Saisissez l\'ODD touché', // Ajouter un placeholder
                ],
               
            ])
            ->add('servicerec', ChoiceType::class, [
                'label' => 'Service',
                'choices' => [
                    'Hygiène' => 'Hygiène',
                    'Sécurité' => 'Sécurité',
                    'Discipline' => 'Discipline',
                    
                ],
                'placeholder' => 'Si vous choisissez qualité comme catégorie', // Ajouter le placeholder ici
                'attr' => ['class' => 'form-control'],
            ])
          
           
         
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
