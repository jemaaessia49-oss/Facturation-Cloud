<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\Societe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroSo', TextType::class, [
                'label' => 'Numéro SO',
                'constraints' => [
                    new NotBlank(['message' => 'Merci de saisir le numéro SO']),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom du projet',
                'constraints' => [
                    new NotBlank(['message' => 'Merci de saisir le nom du projet']),
                ],
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Actif' => 'actif',
                    'Inactif' => 'inactif',
                    'Termine' => 'termine',
                ],
            ])
            ->add('societe', EntityType::class, [
                'class' => Societe::class,
                'choice_label' => 'nom',
                'label' => 'Societe',
                'placeholder' => 'Choisissez une societe',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
