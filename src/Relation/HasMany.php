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

/**
 *
 * Represents a relationship where a native entity has many foreign entities
 * (i.e., a foreign collection); the foreign entities are subordinate to the
 * native one.
 *
 * @package Aura.Marshal
 *
 */
class HasMany extends AbstractRelation implements RelationInterface
{
    /**
     *
     * Returns the related foreign collection for a native entity.
     *
     * @param mixed $entity The native entity.
     *
     * @return GenericCollection
     *
     */
    public function getForEntity($entity)
    {
        $native_field = $this->native_field;
        return $this->foreign->getCollectionByField(
            $this->foreign_field,
            $entity->$native_field
        );
    }
}
