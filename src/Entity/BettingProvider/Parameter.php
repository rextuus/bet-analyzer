<?php

namespace App\Entity\BettingProvider;

use Doctrine\ORM\Mapping as ORM;
use Sherlockode\ConfigurationBundle\Model\Parameter as BaseParameter;


#[ORM\Entity]
#[ORM\Table(name: 'parameter')]
class Parameter extends BaseParameter
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\Column(name: 'path', type: 'string')]
    protected string $path;

    #[ORM\Column(name: 'value', type: 'text', nullable: true)]
    protected ?string $value = null;
}
