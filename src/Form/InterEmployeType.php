<?php

namespace App\Form;

use App\Entity\Chantier;
use App\Entity\Employe;
use App\Entity\InterEmploye;
use App\Entity\Poste;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterEmployeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbHeures')
            ->add('employe', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => function (Employe $empl): string {
                    return $empl->getId().' - '.$empl->getNom().' '.$empl->getPrenom();
                },
            ])
            ->add('chantier', EntityType::class, [
                'class' => Chantier::class,
                'choice_label' => function(Chantier $chant): string {
                    return $chant->getID().' - '.$chant->getReference();
                },
            ])
            ->add('poste', EntityType::class, [
                'class' => Poste::class,
                'choice_label' => function(Poste $poste): string {
                    return $poste->getId().' - '.$poste->getLibelle();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InterEmploye::class,
        ]);
    }
}
