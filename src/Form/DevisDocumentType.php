<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\DevisDocument;
use App\Entity\TypeDocument;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateEnregistrement')
            ->add('dateModification')
            ->add('TypeDocument', EntityType::class, [
                'class' => TypeDocument::class,
                'choice_label' => 'id',
            ])
            ->add('Devis', EntityType::class, [
                'class' => Devis::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevisDocument::class,
        ]);
    }
}
