<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Lieu;
use App\Entity\TagEvenemement;
use App\Enum\Categorie;
use App\Enum\StatutEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4],
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu (description)',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('capaciteMax', IntegerType::class, [
                'label' => 'Capacité maximale',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    '🎤 Conférence' => Categorie::CONFERENCE,
                    '🔧 Atelier'    => Categorie::ATELIER,
                    '👥 Meetup'     => Categorie::MEETUP,
                    '📚 Formation'  => Categorie::FORMATION,
                    '🎵 Concert'    => Categorie::CONCERT,
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    '📝 Brouillon' => StatutEvent::BROUILLON,
                    '🟢 Publié'    => StatutEvent::PUBLIE,
                    '🔴 Complet'   => StatutEvent::COMPLET,
                    '⚫ Annulé'    => StatutEvent::ANNULE,
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('lieu_event', EntityType::class, [
                'label' => 'Lieu',
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder' => '-- Choisir un lieu --',
                'required' => false,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('tagEvenements', EntityType::class, [
                'label' => 'Tags',
                'class' => TagEvenemement::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
                'required' => false,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image (JPEG, PNG, WebP - max 2Mo)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '2M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                        mimeTypesMessage: 'Veuillez uploader une image JPEG, PNG ou WebP.',
                    ),
                ],
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}