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
        $record = new GenericRecord([], $type);
        foreach ((array) $data as $field => $value) {
            $record->$field = $value;
        }
        return $record;
    }
}
