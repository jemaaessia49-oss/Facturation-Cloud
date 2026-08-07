<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'mapped' => false,
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Consultant' => 'ROLE_CONSULTANT',
                ],
                'data' => $options['current_role'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => $options['is_edit']
                    ? 'Nouveau mot de passe (laisser vide pour ne pas changer)'
                    : 'Mot de passe',
                'mapped' => false,
                'required' => !$options['is_edit'],
                'constraints' => $options['is_edit']
                    ? []
                    : [
                        new NotBlank(['message' => 'Merci de saisir un mot de passe']),
                        new Length(['min' => 6, 'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères']),
                    ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
            'current_role' => 'ROLE_CONSULTANT',
        ]);
    }
}