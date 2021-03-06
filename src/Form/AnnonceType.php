<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => "Titre de l'annonce :",
                'attr' => ['placeholder' => "Renseignez le titre de l'annonce"]
            ])
            ->add('contenu',  TextType::class, [
                'label' => "Contenu de l'annonce :",
                'attr' => ['placeholder' => "Décrivez précisément votre annonce"]
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie : ',
                'choices' => [
                    "Emploi" => "Emploi",
                    "Immobilier" => "Immobilier"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
