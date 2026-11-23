<?php

namespace App\Form;

use App\Entity\Chantier;
use App\Entity\ChantierTache;
use App\Entity\Tache;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChantierTacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('valBoolean')
            ->add('valEntier')
            ->add('valFloat')
            ->add('valTexte')
            ->add('valDate')
            ->add('valDateHeure')
            ->add('tache', EntityType::class, [
                'class' => Tache::class,
                'choice_label' => 'id',
            ])
            ->add('chantier', EntityType::class, [
                'class' => Chantier::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChantierTache::class,
        ]);
    }
}
