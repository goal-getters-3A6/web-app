<?php

namespace App\Form;

use App\Entity\Plat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Import FileType
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class PlatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomp', null, [
                'label' => 'Nom : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('prixp', null, [
                'label' => 'Prix : ',
                'invalid_message' => 'Le prix doit être un nombre.',
                'constraints' => [
                    new Positive([
                        'message' => 'Le prix doit être un nombre positif.',
                    ]),
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('descp', null, [
                'label' => 'Description : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('alergiep', null, [
                'label' => 'Allergies : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('photop', FileType::class, [ // Change null to FileType
                'label' => 'Photo : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide.',
                    ]),
                ],
                'mapped' => false, // This is important to prevent file uploads from being persisted directly to the entity
            ])
            ->add('etatp', null, [
                'label' => 'Etat : ',
            ])
            ->add('calories', null, [
                'label' => 'Calories : ',
                'constraints' => [
                    new Positive([
                        'message' => 'Les calories doivent être un nombre positif.',
                    ]),
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Plat::class,
        ]);
    }
}
