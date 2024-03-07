<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class KeyValueListing
{
    public array $content;
    public function getKeys(): array
    {
        return array_keys($this->content);
    }

    public function getValues(): array
    {
        return array_values($this->content);
    }
}
