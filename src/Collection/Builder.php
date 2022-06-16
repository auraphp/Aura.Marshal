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

/**
 *
 * Creates a new collection object for a type.
 *
 * @package Aura.Marshal
 *
 */
class Builder implements BuilderInterface
{
    /**
     *
     * The class to use for new instances.
     *
     * @var string
     *
     */
    protected $class = 'Aura\Marshal\Collection\GenericCollection';

    /**
     *
     * Creates a new collection object.
     *
     * @param array $data Data to load into the collection.
     *
     * @return GenericCollection
     *
     */
    public function newInstance(array $data)
    {
        $class = $this->class;
        return new $class($data);
    }
}
