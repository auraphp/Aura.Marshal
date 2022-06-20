<?php
/**
 *
 * This file is part of the Aura project for PHP.
 *
 * @package Aura.Marshal
 *
 * @license https://opensource.org/licenses/mit-license.php MIT
 *
 */
namespace Aura\Marshal;

use ArrayAccess;

/**
 *
 * An object to allow iteration over the elements of a Data object.
 *
 * @package Aura.Marshal
 *
 */
class DataIterator implements \Iterator
{
    /**
     *
     * The data over which we are iterating.
     *
     * @var ArrayAccess
     *
     */
    protected $data;

    /**
     *
     * The keys to iterate over in the Data object.
     *
     * @var array
     *
     */
    protected $keys;

    /**
     *
     * Is the current iterator position valid?
     *
     * @var bool
     *
     */
    protected $valid;

    /**
     *
     * Constructor.
     *
     * @param ArrayAccess $data The Data object over which to iterate.
     *
     * @param array $keys The keys in the Data object.
     *
     */
    public function __construct(ArrayAccess $data, array $keys = [])
    {
        $this->data = $data;
        $this->keys = $keys;
    }

    /**
     *
     * Returns the value at the current iterator position.
     *
     * @return mixed
     *
     */
    public function current()
    {
        return $this->data->offsetGet($this->key());
    }

    /**
     *
     * Returns the current iterator position.
     *
     * @return mixed
     *
     */
    public function key()
    {
        return current($this->keys);
    }

    /**
     *
     * Moves the iterator to the next position.
     *
     * @return void
     *
     */
    public function next()
    {
        $this->valid = (next($this->keys) !== false);
    }

    /**
     *
     * Moves the iterator to the first position.
     *
     * @return void
     *
     */
    public function rewind()
    {
        $this->valid = (reset($this->keys) !== false);
    }

    /**
     *
     * Is the current iterator position valid?
     *
     * @return void
     *
     */
    public function valid()
    {
        return $this->valid;
    }
}
