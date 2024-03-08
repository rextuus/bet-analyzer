<?php

namespace App\Twig\Components;

use App\Twig\Data\KeyValueListingContainer;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class KeyValueListing
{
    public KeyValueListingContainer $container;
    public function getKeys(): array
    {
        return $this->container->getKeys();
    }

    public function getValues(): array
    {
        return $this->container->getValues();
    }
}
