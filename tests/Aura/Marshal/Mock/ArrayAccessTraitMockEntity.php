<?php
namespace Aura\Marshal\Mock;

use Aura\Marshal\Entity\MagicArrayAccessTrait;

class ArrayAccessTraitMockEntity implements \ArrayAccess
{
    protected $data = [];

    use MagicArrayAccessTrait;

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

}
