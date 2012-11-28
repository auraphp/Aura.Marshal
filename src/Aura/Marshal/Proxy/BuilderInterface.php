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
 * An interface for Proxy builders.
 * 
 * @package Aura.Marshal
 * 
 */
interface BuilderInterface
{
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
    public function newInstance(RelationInterface $relation);
}
