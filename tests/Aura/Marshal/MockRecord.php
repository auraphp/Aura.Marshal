<?php
namespace Aura\Marshal;

use Aura\Marshal\Record\MagicPropertyTrait;

class MockRecord
{
    use MagicPropertyTrait;
    
    protected $foo;
    protected $bar;
    protected $baz;
    protected $related;
}
