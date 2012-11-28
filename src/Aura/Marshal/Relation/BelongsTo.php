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
     * @return GenericEntity
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
