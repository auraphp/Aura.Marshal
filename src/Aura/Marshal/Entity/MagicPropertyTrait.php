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
 * entity class that uses protected properties for its fields.
 * 
 * @package Aura.Marshal
 * 
 */
trait MagicPropertyTrait
{
    /**
     * 
     * Gets a protected property as a field value; converts Lazy objects for
     * related entites and collections in place.
     * 
     * Note that this will *not* be invoked by in-class uses of the protected
     * property; this means proxies will not be converted in those cases.
     * 
     * @param string $field The field name to get.
     * 
     * @return mixed The field value.
     * 
     */
    public function __get($field)
    {
        // get the property value
        $value = $this->$field;
        
        // is it a Lazy placeholder?
        if ($value instanceof LazyInterface) {
            // replace the Lazy placeholder with the real object
            $value = $value->get($this);
            $this->$field = $value;
        }
        
        // done!
        return $value;
    }
    
    /**
     * 
     * Sets a protected property.
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
        $this->$field = $value;
    }
    
    /**
     * 
     * Checks to see if a protected property is set.
     * 
     * @param string $field The field name to check.
     * 
     * @return bool
     * 
     */
    public function __isset($field)
    {
        return isset($this->$field);
    }
    
    /**
     * 
     * Unsets a protected property.
     * 
     * @param string $field The field name to check.
     * 
     * @return bool
     * 
     */
    public function __unset($field)
    {
        unset($this->$field);
    }
}
