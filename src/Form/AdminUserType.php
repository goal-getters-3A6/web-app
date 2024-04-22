<?php

namespace App\Form;

use App\Entity\User;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom', 'constraints' => [new NotBlank(['message' => 'Veuillez entrer un nom'])]])
            ->add('prenom', TextType::class, ['label' => 'Prénom', 'constraints' => [new NotBlank(['message' => 'Veuillez entrer un prénom'])]])
            ->add('mail', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un email valide',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])

            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'CLIENT',
                    'Administrateur' => 'ADMIN',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un rôle',
                    ]),
                ],
                'data' => 'CLIENT',
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                // print data
                // var_dump($data);
                // die();
                if (isset($data['role']) && $data['role'] === 'CLIENT') {
                    $form->add('image', FileType::class, [
                        'label' => 'Image', 'mapped' => false, 'required' => false, 'constraints' => [
                            new Image([
                                'maxSize' => '5M',
                                'mimeTypes' => ['image/jpeg', 'image/png'],
                                'mimeTypesMessage' => 'Veuillez insérer une image valide',
                            ]),
                            new NotBlank(['message' => 'Veuillez insérer une image'])
                        ]
                    ])
                        ->add('dateNaissance', BirthdayType::class, [
                            'attr' => ['class' => 'js-datepicker'],
                            'label' => 'Date de naissance',
                            'constraints' => [
                                new NotBlank([
                                    'message' => 'Vous devez entrer une date de naissance valide',
                                ]),
                                new Callback([$this, 'validateAge']),
                            ],
                            'mapped' => false,
                        ])
                        ->add('tel', TelType::class, ['label' => 'Téléphone', 'constraints' => [
                            new NotBlank(['message' => 'Veuillez entrer un numéro de téléphone']),
                            new Length([
                                'min' => 8,
                                'max' => 8,
                                'exactMessage' => 'Votre numéro de téléphone doit contenir {{ limit }} chiffres'
                            ])
                        ]])
                        ->add('poids', RangeType::class, [
                            'label' => 'Poids',
                            'attr' => [
                                'min' => 0,
                                'max' => 300,
                                'step' => 1,
                                'class' => 'poids-input', // Add class attribute
                            ],
                            'constraints' => [
                                new NotBlank(['message' => 'Veuillez entrer un poids'])
                            ]
                        ])
                        ->add('taille', RangeType::class, [
                            'label' => 'Taille',
                            'attr' => [
                                'min' => 0,
                                'max' => 300,
                                'step' => 1,
                                'class' => 'taille-input', // Add class attribute
                            ],
                            'constraints' => [
                                new NotBlank(['message' => 'Veuillez entrer une taille'])
                            ]
                        ])
                        ->add('sexe', ChoiceType::class, [
                            'choices' => [
                                'Homme' => 'Homme',
                                'Femme' => 'Femme',
                            ],
                            'constraints' => [
                                new NotBlank([
                                    'message' => 'Veuillez choisir un sexe',
                                ]),
                            ],
                        ]);
                }
            })
            ->add('submit', SubmitType::class, ['label' => 'Ajouter']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function validateAge($dateNaissance, ExecutionContextInterface $context): void
    {
        $unvalid = false;
        // Validate age greater than 14
        $today = new \DateTime();
        if ($dateNaissance === null) {
            $unvalid = true;
        } else {
            $age = $today->diff($dateNaissance)->y;
            if ($age < 14) {
                $unvalid = true;
            }
        }
        if ($unvalid) {
            $context->buildViolation('Vous devez avoir au moins 14 ans pour vous inscrire')
                ->atPath('dateNaissance')
                ->addViolation();
        }
    }
}
