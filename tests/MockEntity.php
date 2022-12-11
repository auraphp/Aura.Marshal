<?php
namespace Aura\Marshal;

use Aura\Marshal\Entity\MagicPropertyTrait;
use stdClass;

class MockEntity extends stdClass
{
    use MagicPropertyTrait;

    protected $foo;
    protected $bar;
    protected $baz;
    protected $related;
}
