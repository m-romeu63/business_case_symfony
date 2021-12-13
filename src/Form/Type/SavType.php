<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SavType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options): void 
    {
        $builder
            ->add('order', TextType::class, [
                'label' => 'N° de commande'
            ])
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('email', EmailType::class)
            ->add('motif', ChoiceType::class, [
                'choices' => [
                    'Commande incomplète' => 'Commande incomplète',
                    'Produit périmé' => 'Produit périmé',
                    'Produit défectueux' => 'Produit défectueux',
                    'Pièce cassée' => 'Pièce cassée'
                ]
            ])
            ->add('message', TextareaType::class, [
                'attr' => ['rows' => 6]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-outline-dark']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}