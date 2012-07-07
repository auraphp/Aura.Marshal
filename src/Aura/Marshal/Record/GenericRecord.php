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
     * An array of the data as initially constructed.
     * 
     * @var array
     * 
     */
    protected $initial_data = [];
    
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
        $this->initial_data = $data;
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
    
    /**
     * 
     * Returns the fields that have been changed, and their new values.
     * 
     * If a field has been added, it counts as "changed" regardless of its
     * new value.
     * 
     * If old and new values for a field are numeric (whether string, integer,
     * or float) then they are compare using loose inequality (!=).
     * 
     * In all other cases, old and new values are compared using strict 
     * inequality (!==).
     * 
     * This complexity results from converting string and numeric values in
     * and out of databases.  Coming from the database, a string numeric
     * '1' might be filtered to an integer 1 at some point, making it look
     * like the value was changed when in practice it has not.
     * 
     * @return array An array of key-value pairs where the key is the field
     * name and the value is the new (changed) value.
     * 
     */
    public function getChangedFields()
    {
        // the eventual list of changed fields and values
        $changed = [];
        
        // the list of relations
        $related = $this->type->getRelationNames();
        
        // go through all the data elements and their presumed new values
        foreach ($this->data as $field => $new) {
            
            // if the field is a related record or collection, skip it.
            // technically, we should ask it if it has changed at all.
            if (in_array($field, $related)) {
                continue;
            }
            
            // if the field is not part of the initial data ...
            if (! array_key_exists($field, $this->initial_data)) {
                // ... then it's a change from the initial data.
                $changed[$field] = $new;
                continue;
            }
            
            // what was the old (initial) value?
            $old = $this->initial_data[$field];
            
            // are both old and new values numeric?
            $numeric = is_numeric($old) && is_numeric($new);
            
            // if both old and new are numeric, compare loosely.
            if ($numeric && $old != $new) {
                // loosely different, retain the new value
                $changed[$field] = $new;
            }
            
            // if one or the other is not numeric, compare strictly
            if (! $numeric && $old !== $new) {
                // strictly different, retain the new value
                $changed[$field] = $new;
            }
        }
        
        // done!
        return $changed;
    }
}
