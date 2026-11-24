<?php

namespace App\Twig\Components\Navigation;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class NavHeader
{
    public string $label;
}
