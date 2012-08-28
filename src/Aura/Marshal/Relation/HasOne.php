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
 * Represents a relationship where the native record has one of the foreign 
 * record; the foreign record is subordinate to the native one.
 * 
 * @package Aura.Marshal
 * 
 */
class HasOne extends AbstractRelation implements RelationInterface
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
