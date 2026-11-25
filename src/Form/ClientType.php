<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Civilite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civilite1', EntityType::class, [
                'class' => Civilite::class,
                'choice_label' => 'nom_long',
            ])
            ->add('nom')
            ->add('prenom')
            ->add('lieu_naissance')
            ->add('telephone1')
            ->add('mail1')
            ->add('dateNaissance1', DateType::class)

            ->add('civilite2', EntityType::class, [
                'class' => Civilite::class,
                'choice_label' => 'nom_long',
                'required' => false,
            ])

            ->add('nom2', null, ['required' => false])
            ->add('prenom2', null, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}