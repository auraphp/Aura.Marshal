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
namespace Aura\Marshal\Lazy;

use Aura\Marshal\Relation\RelationInterface;

/**
 * 
 * An interface for Lazy builders.
 * 
 * @package Aura.Marshal
 * 
 */
interface BuilderInterface
{
    /**
     * 
     * Creates a new Lazy object.
     * 
     * @param RelationInterface $relation The relationship object between the
     * native and foreign types.
     * 
     * @return Lazy
     * 
     */
    public function newInstance(RelationInterface $relation);
}
