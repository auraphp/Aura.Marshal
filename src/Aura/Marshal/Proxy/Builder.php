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
namespace Aura\Marshal\Proxy;

use Aura\Marshal\Relation\RelationInterface;

/**
 * 
 * A builder for Proxy objects.
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
    protected $class = 'Aura\Marshal\Proxy\GenericProxy';
    
    /**
     * 
     * Creates a new Proxy object.
     * 
     * @param RelationInterface $relation The relationship object between the
     * native and foreign types.
     * 
     * @return Proxy
     * 
     */
    public function newInstance(RelationInterface $relation)
    {
        $class = $this->class;
        return new $class($relation);
    }
}
