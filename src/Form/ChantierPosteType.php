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
        $builder
            ->add('montantVenduHT')
            ->add('montantFournituresHT')
            ->add('chantier', EntityType::class, [
                'class' => Chantier::class,
                'choice_label' => 'id',
            ])
            ->add('poste', EntityType::class, [
                'class' => Poste::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChantierPoste::class,
        ]);
    }
}
