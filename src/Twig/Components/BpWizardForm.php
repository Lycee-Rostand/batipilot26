<?php

namespace App\Twig\Components;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class BpWizardForm
{
    public string $titre;
    public string $sousTitre;
    public bool $subCard = false;
    public ?string $subCardTitre;
    public FormView $form;
}
