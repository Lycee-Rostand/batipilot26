<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Cta
{
    public string $route;
    public string $message;
    public ?string $icon = null;
    public string $couleur = "primary";
    public bool $secondaire = false;
    public bool $retour = false;
}
