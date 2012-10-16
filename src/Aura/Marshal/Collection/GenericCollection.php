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
namespace Aura\Marshal\Collection;

use Aura\Marshal\Data;
use Aura\Marshal\Type\GenericType;

/**
 * 
 * Represents a generic collection of records.
 * 
 * @package Aura.Marshal
 * 
 */
class GenericCollection extends Data
{
    /**
     * 
     * The type for this collection.
     * 
     * @var GenericType
     * 
     */
    protected $type;

    /**
     * 
     * Constructor.
     * 
     * @param array $data An array of records for this collection.
     * 
     * @param GenericType $type The type for this collection.
     * 
     */
    public function __construct(array $data, GenericType $type)
    {
        parent::__construct($data);
        $this->type = $type;
    }

    /**
     * 
     * Returns an array of all the identity values for the collection.
     * 
     * This will not convert the collection elements to record objects.
     *
     * @return array
     * 
     */
    public function getIdentityValues()
    {
        $identity_field = $this->type->getIdentityField();
        return $this->getFieldValues($identity_field);
    }

    /**
     * 
     * Returns an array of all values for a single field in the collection.
     * 
     * This will not convert the collection elements to record objects.
     * 
     * @param string $field The field name to retrieve values for.
     *
     * @return array
     * 
     */
    public function getFieldValues($field)
    {
        $values = [];
        foreach ($this->data as $offset => $record) {
            $values[$offset] = $record->$field;
        }
        return $values;
    }

    /**
     * 
     * Is the collection empty?
     * 
     * @return bool
     * 
     */
    public function isEmpty()
    {
        return empty($this->data);
    }

    /**
     * 
     * Adds a new record to the collection (and to the IdentityMap for the
     * type).
     * 
     * @param array $data Data for the new record.
     * 
     * @return object
     * 
     */
    public function appendNewRecord(array $data = [])
    {
        $record = $this->type->newRecord($data);
        $this->data[] = $record;
        return $record;
    }
}
