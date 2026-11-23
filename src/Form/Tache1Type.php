<?php

namespace App\Form;

use App\Entity\Poste;
use App\Entity\Tache;
use App\Entity\TacheFormat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Tache1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('archive')
            ->add('TacheFormat', EntityType::class, [
                'class' => TacheFormat::class,
                'choice_label' => 'nom',
            ])
            ->add('PosteId', EntityType::class, [
                'class' => Poste::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
