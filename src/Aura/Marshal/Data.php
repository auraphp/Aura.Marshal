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
namespace Aura\Marshal;

/**
 * 
 * Represents a data set. This is similar to the SPL ArrayObject, but allows
 * you easier access to the underlying data itself. This class serves as a
 * base for ...
 * 
 * - the generic type object, where $data represents an IdentityMap;
 * 
 * - the generic entity object, where $data represents the entity fields; and
 * 
 * - the generic collection object, where $data represents an array of 
 *   entities.
 * 
 * @package Aura.Marshal
 * 
 */
class Data implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * 
     * Key-value pairs of data.
     * 
     * @var array
     * 
     */
    protected $data = [];

    /**
     * 
     * Constructor.
     * 
     * @param array $data The data for this object.
     * 
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * 
     * ArrayAccess: does the requested key exist?
     * 
     * @param int|string $key The requested key.
     * 
     * @return bool
     * 
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * 
     * ArrayAccess: get a key value.
     * 
     * @param int|string $key The requested key.
     * 
     * @return mixed
     * 
     */
    public function offsetGet($key)
    {
        return $this->data[$key];
    }

    /**
     * 
     * ArrayAccess: set a key value.
     * 
     * @param int|string $key The requested key.
     * 
     * @param mixed $val The value to set it to.
     * 
     * @return void
     * 
     */
    public function offsetSet($key, $val)
    {
        $this->data[$key] = $val;
    }

    /**
     * 
     * ArrayAccess: unset a key.
     * 
     * @param int|string $key The requested key.
     * 
     * @return void
     * 
     */
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * 
     * Countable: how many keys are there?
     * 
     * @return int
     * 
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * 
     * IteratorAggregate: returns an external iterator for this struct.
     * 
     * @return DataIterator
     * 
     */
    public function getIterator()
    {
        return new DataIterator($this, array_keys($this->data));
    }
}
