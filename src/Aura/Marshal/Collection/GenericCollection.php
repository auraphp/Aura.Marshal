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

use Aura\Marshal\Data;
use Aura\Marshal\Type\GenericType;

/**
 * 
 * Represents a generic collection of entities.
 * 
 * @package Aura.Marshal
 * 
 */
class GenericCollection extends Data
{
    /**
     * 
     * Returns an array of all values for a single field in the collection.
     * 
     * @param string $field The field name to retrieve values for.
     *
     * @return array
     * 
     */
    public function getFieldValues($field)
    {
        $values = [];
        foreach ($this->data as $offset => $entity) {
            $values[$offset] = $entity->$field;
        }
        return $values;
    }

    /**
     * 
     * Is the collection empty?
     * 
     * @return bool
     * 
     */
    public function isEmpty()
    {
        return empty($this->data);
    }
}
