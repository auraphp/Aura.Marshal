<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Marshal\Collection;
use Aura\Marshal\Data;
use Aura\Marshal\Type\GenericType;

/**
 * 
 * Represents a generic collection of records.
 * 
 * @package Aura.Marshal
 * 
 */
class GenericCollection extends Data
{
    /**
     * 
     * The type for this collection.
     * 
     * @var GenericType
     * 
     */
    protected $type;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $data An array of records for this collection.
     * 
     * @param GenericType $type The type for this collection.
     * 
     */
    public function __construct(array $data, GenericType $type)
    {
        parent::__construct($data);
        $this->type = $type;
    }
    
    /**
     * 
     * ArrayAccess: Get a key value.
     * 
     * This override from the parent::offsetGet() lets us convert records
     * lazily using the IdentityMap for the type.
     * 
     * @param string $key The requested key.
     * 
     * @return mixed
     * 
     */
    public function offsetGet($key)
    {
        $record_class = $this->type->getRecordClass();
        if (! $this->data[$key] instanceof $record_class) {
            $identity_field = $this->type->getIdentityField();
            $identity_value = $this->data[$key]->$identity_field;
            $this->data[$key] = $this->type->getRecord($identity_value);
        }
        
        return $this->data[$key];
    }
    
    /**
     * 
     * Returns an array of all the identity values for the collection.
     * 
     * This will not convert the collection elements to record objects.
     * 
     * @param string $field The field name to retrieve values for.
     *
     * @return array
     * 
     */
    public function getIdentityValues()
    {
        $identity_field = $this->type->getIdentityField();
        return $this->getFieldValues($identity_field);
    }
    
    /**
     * 
     * Returns an array of all values for a single field in the collection.
     * 
     * This will not convert the collection elements to record objects.
     * 
     * @param string $field The field name to retrieve values for.
     *
     * @return array
     * 
     */
    public function getFieldValues($field)
    {
        $values = array();
        foreach ($this->data as $offset => $record) {
            $values[$offset] = $record->$field;
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
