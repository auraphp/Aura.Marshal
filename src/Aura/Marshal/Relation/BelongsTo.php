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
 * Represents a relationship where the native record belongs to a foreign 
 * record; the native record is subordinate to the foreign one.
 * 
 * @package Aura.Marshal
 * 
 */
class BelongsTo extends AbstractRelation implements RelationInterface
{
    /**
     * 
     * Returns the related foreign record for a native record.
     * 
     * @param mixed $record The native record.
     * 
     * @return GenericRecord
     * 
     */
    public function getForRecord($record)
    {
        $native_field = $this->native_field;
        return $this->foreign_type->getRecordByField(
            $this->foreign_field,
            $record->$native_field
        );
    }
}
