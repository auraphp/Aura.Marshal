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
use Aura\Marshal\Exception;
use Aura\Marshal\Manager;
use Aura\Marshal\Type\GenericType;

/**
 *
 * Represents a relationship where a native entity has many foreign entities
 * (i.e., a foreign collection) mapped through an association type.
 *
 * @package Aura.Marshal
 *
 */
class HasManyThrough extends AbstractRelation implements RelationInterface
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
        if (isset($this->through, $this->through_native_field, $this->through_foreign_field)) {
            // first, find the native values in the through type
            $native_field = $this->native_field;
            $native_value = $entity->$native_field;
            $through_coll = $this->through->getCollectionByField(
                $this->through_native_field,
                $native_value
            );
    
            // now find the foreign values from the through collection
            $foreign_values = $through_coll->getFieldValues(
                $this->through_foreign_field
            );
        }

        // finally, return a foreign collection based on the foreign values
        return $this->foreign->getCollectionByField(
            $this->foreign_field,
            $foreign_values ?? []
        );
    }
}
