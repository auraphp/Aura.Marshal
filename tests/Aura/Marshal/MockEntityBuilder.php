<?php
namespace Aura\Marshal;

use Aura\Marshal\Entity\Builder;

class MockEntityBuilder extends Builder
{
    /**
     * MockEntityBuilder constructor.
     *
     * @param string $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }
}
