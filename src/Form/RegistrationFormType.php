<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer votre prénom.'),
                    new Length(max: 255),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre prénom',
                    'autofocus' => true,
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer votre nom.'),
                    new Length(max: 255),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre nom',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer votre email.'),
                    new Email(message: 'Veuillez entrer un email valide.'),
                    new Length(max: 180),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre email',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => '············',
                        'autocomplete' => 'new-password',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => '············',
                        'autocomplete' => 'new-password',
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer un mot de passe.'),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        max: 4096,
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
