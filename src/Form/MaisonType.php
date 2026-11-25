<?php

namespace App\Form;

use App\Entity\Maison;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\TypeCharpente;
use App\Entity\Client;


class MaisonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rue')
            ->add('copos', null, [
                'label' => 'Code postal',
            ])
            ->add('ville')
            /* pas de client dans le formulaire d'après madame Annouche 
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir un client',
            ])
                */
            ->add('typeCharpente', EntityType::class, [
                'class' => TypeCharpente::class,
                'choice_label' => 'nom',
            ])
            ->add('anneeConstruction', DateType::class, [
                'label' => 'Année de construction',
                'widget' => 'single_text',
            ])
            ->add('sectionCadastrale', null, [
                'label' => 'Section cadastrale',
            ])
            ->add('superficieSectionCadastrale', IntegerType::class, [
                'label' => 'Superficie de la section cadastrale (m²)',
                'required' => false,
            ])
            ->add('maisonPhenix', CheckboxType::class, [
                'label' => 'Maison Phénix',
                'required' => false,
            ])
            ->add('distanceDepot', IntegerType::class, [
                'label' => 'Distance au dépôt (km)',
            ])
            ->add('tempsTrajet', IntegerType::class, [
                'label' => 'Temps de trajet (min)',
                'required' => false,
            ])
            ->add('surfaceHabitable', IntegerType::class, [
                'label' => 'Surface habitable (m²)',
            ])
            ->add('surfaceComblesCree', IntegerType::class, [
                'label' => 'Surface des combles créée (m²)',
            ])
            ->add('remarques', TextareaType::class, [
                'label' => 'Remarques',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Maison::class,
        ]);
    }
}
