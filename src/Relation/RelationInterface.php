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
namespace Aura\Marshal\Relation;

use Aura\Marshal\Collection\GenericCollection;
use Aura\Marshal\Entity\GenericEntity;

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
     * @return object|GenericEntity|GenericCollection
     *
     */
    public function getForEntity($entity);

    /**
     *
     * Gets the name of the foreign type in the manager.
     *
     * @return string
     *
     */
    public function getForeignType();
}
