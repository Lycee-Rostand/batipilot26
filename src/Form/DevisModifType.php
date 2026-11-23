<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\DevisModif;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisModifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('version')
            ->add('dateModif')
            ->add('objet')
            ->add('Devis', EntityType::class, [
                'class' => Devis::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevisModif::class,
        ]);
    }
}
