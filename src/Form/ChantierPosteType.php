<?php

namespace App\Form;

use App\Entity\Chantier;
use App\Entity\ChantierPoste;
use App\Entity\Poste;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChantierPosteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['lock_chantier']) {
            $builder->add('chantier', EntityType::class, [
                'class' => Chantier::class,
                'choice_label' => 'reference',
                'label' => 'Chantier',
            ]);
        }

        $builder
            ->add('poste', EntityType::class, [
                'class' => Poste::class,
                'choice_label' => 'libelle',
                'label' => 'Poste',
            ])
            ->add('montantVenduHT', null, [
                'label' => 'Montant vendu HT (€)',
            ])
            ->add('montantFournituresHT', null, [
                'label' => 'Montant fournitures HT (€)',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChantierPoste::class,
            'lock_chantier' => false,
        ]);

        $resolver->setAllowedTypes('lock_chantier', 'bool');
    }
}