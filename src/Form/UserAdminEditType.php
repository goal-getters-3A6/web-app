<?php

namespace App\Form;

use App\Entity\User;
use PharIo\Manifest\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class UserAdminEdit extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, ['label' => 'Nom'])
            ->add('prenom', null, ['label' => 'Prénom'])
            ->add('mail', EmailType::class, ['label' => 'Email'])
            ->add('image', FileType::class, ['label' => 'Image', 'mapped' => false, 'required' => false, 'constraints' => [new Image([
                'maxSize' => '5M',
                'mimeTypes' => ['image/jpeg', 'image/png'],
                'mimeTypesMessage' => 'Veuillez insérer une image valide',
            ])]])
            ->add('dateNaissance', DateType::class, [
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('tel', NumberType::class, ['label' => 'Téléphone'])
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
