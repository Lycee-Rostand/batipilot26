<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; 
use App\Entity\Civilite;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civilite1', EntityType::class, array('class' => Civilite::class,'choice_label' => 'nom_long' ))
            ->add('nom')
            ->add('prenom')
            ->add('lieu_naissance')
            ->add('telephone1')
            ->add('mail1')
            ->add('dateNaissance1', DateType::class)
            ->add('civilite2', EntityType::class, array('class' => Civilite::class,'choice_label' => 'nom_long' ))
            ->add('nom2')
            ->add('prenom2')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
