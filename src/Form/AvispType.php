<?php

namespace App\Form;

use App\Entity\Avisp;
use App\Entity\Plat;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Security\Core\Security;

class AvispType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $this->security->getUser();
        
        $builder
            ->add('commap', null, [
                'label' => 'Commentaire : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ commentaire ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('star', null, [
                'label' => 'Star Rating : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ star rating ne peut pas être vide.',
                    ]),
                    new Range([
                        'min' => 0,
                        'max' => 5,
                        'minMessage' => 'La note doit être positive.',
                        'maxMessage' => 'La note ne peut pas dépasser 5.',
                    ]),
                ],
            ])
            ->add('fav', null, [
                'label' => 'Favorite ?',
            ])
            ->add('idplat', HiddenType::class, [
                'data' => $options['idplat'], 
                'mapped' => false, 
            ])
            /*->add('iduap', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'label' => 'Utilisateur',
                'data' => $currentUser, 
            ]);*/;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avisp::class,
            'default_user' => null,
            'idplat' => null, 
        ]);
    }
}
