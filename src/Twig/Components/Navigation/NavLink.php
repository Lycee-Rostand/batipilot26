<?php

namespace App\Twig\Components\Navigation;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class NavLink
{
    public string $route;
    public string $label;
    public ?string $icon;
}
