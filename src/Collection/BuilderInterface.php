<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Marshal\Collection;
use Aura\Marshal\Type\GenericType;

/**
 * 
 * An inteface for collection builder objects.
 * 
 * @package Aura.Marshal
 * 
 */
interface BuilderInterface
{
    /**
     * 
     * Creates a new collection object.
     * 
     * @param GenericType $type The type for this collection.
     * 
     * @param array $data Data to load into the collection.
     * 
     */
    public function newInstance(GenericType $type, array $data);
}
