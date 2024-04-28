<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('mail', EmailType::class, ['label' => 'Email', 'disabled' => true])
            ->add('nom', TextType::class, ['label' => 'Nom', 'constraints' => [new NotBlank(['message' => 'Veuillez entrer un nom'])]])
            ->add('prenom', TextType::class, ['label' => 'Prénom', 'constraints' => [new NotBlank(['message' => 'Veuillez entrer un prénom'])]])
            ->add('tel', TelType::class, ['label' => 'Téléphone', 'constraints' => [
                new NotBlank(['message' => 'Veuillez entrer un numéro de téléphone']),
                new Length(
                    [
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Le numéro de téléphone doit contenir {{ limit }} chiffres',
                    ]
                )
            ]])
            ->add('date_naissance',  BirthdayType::class, [
                'attr' => ['class' => 'js-datepicker'],
                'label' => 'Date de naissance',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer une date de naissance valide',
                    ]),
                    new Callback([$this, 'validateAge']),
                ],

            ])
            ->add('poids', RangeType::class, [
                'label' => 'Poids', 'attr' => ['min' => 0, 'max' => 200, 'step' => 1, 'class' => 'poids-input'],
                'constraints' => [new NotBlank(['message' => 'Veuillez entrer un poids'])]
            ])
            ->add('taille', RangeType::class, [
                'label' => 'Taille', 'attr' => ['min' => 0, 'max' => 200, 'step' => 1, 'class' => 'taille-input'],
                'constraints' => [new NotBlank(['message' => 'Veuillez entrer une taille'])]
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
            ])
            ->add('submit', SubmitType::class, ['label' => 'Modifier']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function validateAge($dateNaissance, ExecutionContextInterface $context): void
    {
        // Validate age greater than 14
        $today = new \DateTime();
        $diff = $today->diff($dateNaissance);

        if ($diff->y < 14) {
            $context->buildViolation('Vous devez avoir au moins 14 ans pour vous inscrire.')
                ->atPath('dateNaissance')
                ->addViolation();
        }
    }
}
