<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Marshal\Record;
use Aura\Marshal\Type\GenericType;

/**
 * 
 * Creates a new record object for a type.
 * 
 * @package Aura.Marshal
 * 
 */
class Builder implements BuilderInterface
{
    /**
     * 
     * Creates a new record object.
     * 
     * @param GenericType $type The type for this record.
     * 
     * @param array|object $data Data to load into the record.
     * 
     * @return GenericRecord
     * 
     */
    public function newInstance(GenericType $type, $data)
    {
        return new GenericRecord((array) $data, $type);
    }
}
