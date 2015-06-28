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
namespace Aura\Marshal\Relation;

use Aura\Marshal\Collection\GenericCollection;

/**
 *
 * An interface for relationship description objects.
 *
 * @package Aura.Marshal
 *
 */
interface RelationInterface
{
    /**
     *
     * Returns the foreign entity or collection for a native entity.
     *
     * @param mixed $entity The entity to get the related entity or
     * collection for.
     *
     * @return object|GenericCollection
     *
     */
    public function getForEntity($entity);
}
