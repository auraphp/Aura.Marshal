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

use Aura\Marshal\Entity\GenericEntity;

/**
 *
 * Represents a relationship where the native entity belongs to a foreign
 * entity; the native entity is subordinate to the foreign one.
 *
 * @package Aura.Marshal
 *
 */
class BelongsTo extends AbstractRelation implements RelationInterface
{
    /**
     *
     * Returns the related foreign entity for a native entity.
     *
     * @param mixed $entity The native entity.
     *
     * @return ?object
     *
     */
    public function getForEntity($entity)
    {
        $native_field = $this->native_field;
        return $this->foreign->getEntityByField(
            $this->foreign_field,
            $entity->$native_field
        );
    }
}
