<?php
namespace Aura\Marshal\Mock;

use Aura\Marshal\Entity\MagicPropertyTrait;

class PropertyTraitMockEntity
{
    use MagicPropertyTrait;

    protected $foo;
    protected $bar;
    protected $baz;
    protected $related;
}
