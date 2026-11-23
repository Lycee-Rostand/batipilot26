<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\EtatDevis;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateCreation')
            ->add('dateValidite')
            ->add('refInterne')
            ->add('dateSignature')
            ->add('dateRDVClient')
            ->add('etatDevis', EntityType::class, array('class' => 'App\Entity\EtatDevis','choice_label' => 'nom' ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
