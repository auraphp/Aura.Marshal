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
namespace Aura\Marshal\Collection;

use Aura\Marshal\Type\GenericType;

/**
 * 
 * Creates a new collection object for a type.
 * 
 * @package Aura.Marshal
 * 
 */
class Builder implements BuilderInterface
{
    /**
     * 
     * Creates a new collection object.
     * 
     * @param array $data Data to load into the collection.
     * 
     * @return GenericCollection
     * 
     */
    public function newInstance(array $data)
    {
        return new GenericCollection($data);
    }
}
