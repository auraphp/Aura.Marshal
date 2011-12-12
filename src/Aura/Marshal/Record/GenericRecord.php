<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Marshal\Record;
use Aura\Marshal\Data;
use Aura\Marshal\Type\GenericType;

/**
 * 
 * Represents a single record.
 * 
 * @package Aura.Marshal
 * 
 */
class GenericRecord extends Data
{
    /**
     * 
     * The type for this record.
     * 
     * @var GenericType
     * 
     */
    protected $type;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $data An array of fields for this record.
     * 
     * @param GenericType $type The type for this record.
     * 
     */
    public function __construct(array $data, GenericType $type)
    {
        parent::__construct($data);
        $this->type = $type;
    }
    
    /**
     * 
     * Gets the value of a field by name.
     * 
     * @param string $field The requested field name.
     * 
     * @return mixed The field value.
     * 
     */
    public function __get($field)
    {
        return $this->offsetGet($field);
    }
    
    /**
     * 
     * Sets a the value of a field by name.
     * 
     * @param string $field The requested field name.
     * 
     * @param mixed $value The value to set the field to.
     * 
     * @return void
     * 
     */
    public function __set($field, $value)
    {
        return $this->offsetSet($field, $value);
    }
    
    /**
     * 
     * Does a certain field exist in the record?
     * 
     * @param string $field The requested field name.
     * 
     * @return bool
     * 
     */
    public function __isset($field)
    {
        return $this->offsetExists($field);
    }
    
    /**
     * 
     * Unsets a field in the record.
     * 
     * @param string $field The requested field name.
     * 
     * @return void
     * 
     */
    public function __unset($field)
    {
        $this->offsetUnset($field);
    }
    
    /**
     * 
     * ArrayAccess: Gets a field value by name; if the field is based on a 
     * relation to a foreign type, this will get the related record or 
     * collection.
     * 
     * @param string $field The requested field name.
     * 
     * @return mixed The field value.
     * 
     */
    public function offsetGet($field)
    {
        // if the offset does not exist, and it's a related field,
        // fill it with related data
        $fill_related = ! $this->offsetExists($field)
                      && in_array($field, $this->type->getRelationNames());
        
        if ($fill_related) {
            $relation = $this->type->getRelation($field);
            $value    = $relation->getForRecord($this);
            $this->offsetSet($field, $value);
        }
        
        return parent::offsetGet($field);
    }
    
    /**
     * 
     * ArrayAccess: Unsets a field in the record; this leaves the array key 
     * in place and sets it to null.
     * 
     * @param string $field The requested field name.
     * 
     * @return void
     * 
     */
    public function offsetUnset($field)
    {
        $this->data[$field] = null;
    }
}
