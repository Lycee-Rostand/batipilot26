<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class BpSection
{
    public string $nom;
    public string $titre;
    public string $sousTitre;
    public ?int $nombre;
    public array $colonnes;
    public string $messageEmpty;
    public string $couleur = "neutral";
    public array $elements;
    public array $proprietesElement = [];
}
