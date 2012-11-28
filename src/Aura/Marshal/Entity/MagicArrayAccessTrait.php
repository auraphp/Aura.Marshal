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

use Aura\Marshal\Lazy\LazyInterface;

/**
 * 
 * Use this trait for magic __get(), __set(), __isset(), and __unset() on any
 * entity class that implements ArrayAccess for its fields.
 * 
 * @package Aura.Marshal
 * 
 */
trait MagicArrayAccessTrait
{
    /**
     * 
     * Calls ArrayAccess::offsetGet() to get a field value; converts Lazy
     * objects for related entites and collections in place.
     * 
     * @param string $field The field name to get.
     * 
     * @return mixed The field value.
     * 
     */
    public function __get($field)
    {
        // get the field value
        $value = $this->offsetGet($field);
        
        // is it a Lazy placeholder?
        if ($value instanceof LazyInterface) {
            // replace the Lazy placeholder with the real object
            $value = $value->get($this);
            $this->offsetSet($field, $value);
        }
        
        // done!
        return $value;
    }
    
    /**
     * 
     * Calls ArrayAccess::offsetSet() to set a field value.
     * 
     * @param string $field The field name to get.
     * 
     * @param mixed $value Set the field to this value.
     * 
     * @return void
     * 
     */
    public function __set($field, $value)
    {
        $this->offsetSet($field, $value);
    }
    
    /**
     * 
     * Calls ArrayAccess::offsetExists() to see if a field is set.
     * 
     * @param string $field The field name to check.
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
     * Calls ArrayAccess::offsetUnset() to unset a field.
     * 
     * @param string $field The field name to unset.
     * 
     * @return void
     * 
     */
    public function __unset($field)
    {
        $this->offsetUnset($field);
    }
}
