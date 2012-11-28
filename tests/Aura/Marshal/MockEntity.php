<?php
namespace Aura\Marshal;

use Aura\Marshal\Entity\MagicPropertyTrait;

class MockEntity
{
    use MagicPropertyTrait;
    
    protected $foo;
    protected $bar;
    protected $baz;
    protected $related;
}
