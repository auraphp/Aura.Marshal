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
 * Represents a relationship where a native record has many foreign records
 * (i.e., a foreign collection); the foreign records are subordinate to the
 * native one.
 * 
 * @package Aura.Marshal
 * 
 */
class HasMany extends AbstractRelation implements RelationInterface
{
    /**
     * 
     * Returns the related foreign collection for a native record.
     * 
     * @param mixed $record The native record.
     * 
     * @return GenericCollection
     * 
     */
    public function getForRecord($record)
    {
        $native_field = $this->native_field;
        return $this->foreign_type->getCollectionByField(
            $this->foreign_field,
            $record->$native_field
        );
    }
}
 