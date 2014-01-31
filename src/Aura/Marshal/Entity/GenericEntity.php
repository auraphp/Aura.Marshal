<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Marshal
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Marshal\Entity;

use Aura\Marshal\Data;

/**
 * 
 * Represents a single entity.
 * 
 * @package Aura.Marshal
 * 
 */
class GenericEntity extends Data
{
    use MagicArrayAccessTrait;

    /**
     *
     * ArrayAccess: get a key value.
     *
     * @param int|string $key The requested key.
     *
     * @return mixed
     *
     */
    public function offsetGet($field)
    {
        // get the field value
        $value = $this->data[$field];

        // is it a Lazy placeholder?
        if ($value instanceof LazyInterface) {
            // replace the Lazy placeholder with the real object
            $value = $value->get($this);
        }

        // done!
        return $value;
    }
}
