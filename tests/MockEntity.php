<?php
namespace Aura\Marshal;

use Aura\Marshal\Entity\GenericEntity;
use Aura\Marshal\Entity\MagicPropertyTrait;

class MockEntity extends GenericEntity
{
    use MagicPropertyTrait;

    /** @var mixed */
    protected $foo;

    /** @var mixed */
    protected $bar;

    /** @var mixed */
    protected $baz;

    /** @var mixed */
    protected $related;
}
