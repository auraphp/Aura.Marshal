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
namespace Aura\Marshal\Type;

use Aura\Marshal\Collection\BuilderInterface as CollectionBuilderInterface;
use Aura\Marshal\Data;
use Aura\Marshal\Exception;
use Aura\Marshal\Record\BuilderInterface as RecordBuilderInterface;
use Aura\Marshal\Relation\RelationInterface;

/**
 * 
 * Describes a particular type within the domain, and retains an IdentityMap
 * of records for the type. Converts loaded data to record objects lazily.
 * 
 * @package Aura.Marshal
 * 
 */
class GenericType extends Data
{
    /**
     * 
     * A builder to create collection objects for this type.
     * 
     * @var object
     * 
     */
    protected $collection_builder;

    /**
     * 
     * The record field representing its unique identifier value. The
     * IdentityMap will be keyed on these values.
     * 
     * @var string
     * 
     */
    protected $identity_field;

    /**
     * 
     * An index of records on the identity field. The format is:
     * 
     *      $index_identity[$identity_value] = $offset;
     * 
     * Note that we always have only one offset, keyed by identity value.
     * 
     * @var array
     * 
     */
    protected $index_identity;

    /**
     * 
     * An index of all records added via newRecord(). The format is:
     * 
     *      $index_new[] = $offset;
     * 
     * Note that we always have one offset, and the key is merely sequential.
     * 
     * @var array
     * 
     */
    protected $index_new;

    /**
     * 
     * An array of fields to index on for quicker lookups. The array format
     * is:
     * 
     *     $index_fields[$field_name][$field_value] = (array) $offsets;
     * 
     * Note that we always have an array of offsets, and the keys are by
     * the field name and the values for that field.
     * 
     * @var array
     * 
     */
    protected $index_fields = [];

    /**
     * 
     * A builder to create record objects for this type.
     * 
     * @var object
     * 
     */
    protected $record_builder;

    /**
     * 
     * An array of relationship descriptions, where the key is a
     * field name for the record and the value is a relation object.
     * 
     * @var array
     * 
     */
    protected $relation = [];

    /**
     * 
     * Sets the name of the field that uniquely identifies each record for
     * this type.
     * 
     * @param string $identity_field The identity field name.
     * 
     * @return void
     * 
     */
    public function setIdentityField($identity_field)
    {
        $this->identity_field = $identity_field;
    }

    /**
     * 
     * Returns the name of the field that uniquely identifies each record of
     * this type.
     * 
     * @return string
     * 
     */
    public function getIdentityField()
    {
        return $this->identity_field;
    }

    /**
     * 
     * Sets the fields that should be indexed at load() time; removes all
     * previous field indexes.
     * 
     * @param array $fields The fields to be indexed.
     * 
     * @return void
     * 
     */
    public function setIndexFields(array $fields = [])
    {
        $this->index_fields = [];
        foreach ($fields as $field) {
            $this->index_fields[$field] = [];
        }
    }

    /**
     * 
     * Returns the list of indexed field names.
     * 
     * @return array
     * 
     */
    public function getIndexFields()
    {
        return array_keys($this->index_fields);
    }

    /**
     * 
     * Sets the builder object to create record objects.
     * 
     * @param RecordBuilderInterface $record_builder The builder object.
     * 
     * @return void
     * 
     */
    public function setRecordBuilder(RecordBuilderInterface $record_builder)
    {
        $this->record_builder = $record_builder;
    }

    /**
     * 
     * Returns the builder that creates record objects.
     * 
     * @return object
     * 
     */
    public function getRecordBuilder()
    {
        return $this->record_builder;
    }

    /**
     * 
     * Sets the builder object to create collection objects.
     * 
     * @param CollectionBuilderInterface $collection_builder The builder object.
     * 
     * @return void
     * 
     */
    public function setCollectionBuilder(CollectionBuilderInterface $collection_builder)
    {
        $this->collection_builder = $collection_builder;
    }

    /**
     * 
     * Returns the builder that creates collection objects.
     * 
     * @return object
     * 
     */
    public function getCollectionBuilder()
    {
        return $this->collection_builder;
    }

    /**
     * 
     * Loads the IdentityMap for this type with data for record objects. 
     * 
     * Typically, the $data value is a sequential array of associative arrays. 
     * As long as the $data value can be iterated over and accessed as an 
     * array, you can pass in any kind of $data.
     * 
     * The elements from $data will be placed into the IdentityMap and indexed
     * according to the value of their identity field.
     * 
     * You can call load() multiple times, but records already in the 
     * IdentityMap will not be overwritten.
     * 
     * The loaded elements are cast to objects; this allows consistent
     * addressing of elements before and after conversion to record objects.
     * 
     * The loaded elements will be converted to record objects by the
     * record builder only as you request them from the IdentityMap.
     * 
     * @param array $data Record data to load into the IdentityMap.
     * 
     * @param string $return_field Return values from this field; if empty,
     * return values from the identity field (the default).
     * 
     * @return array The return values from the data elements, regardless
     * of whether they were loaded or not.
     * 
     */
    public function load($data, $return_field = null)
    {
        // what indexes do we need to track?
        $index_fields = array_keys($this->index_fields);

        // return a list of field values in $data
        $return_values = [];

        // what is the identity field for the type?
        $identity_field  = $this->getIdentityField();

        // what should the return field be?
        if (! $return_field) {
            $return_field = $identity_field;
        }
        
        // what's the last data offset?
        $offset = count($this->data);

        // load each data element as a record
        foreach ($data as $record) {

            // cast the element to an object for consistent addressing
            $record = (object) $record;

            // retain the return value on the record
            $return_value    = $record->$return_field;
            $return_values[] = $return_value;

            // retain the identity value on the record
            $identity_value = $record->$identity_field;

            // does the identity already exist in the map?
            if (isset($this->index_identity[$identity_value])) {
                // yes, skip it and go on
                continue;
            }

            // no, retain it in the identity map and identity index ...
            $record = $this->record_builder->newInstance($this, $record);
            $this->data[$offset] = $record;
            $this->index_identity[$identity_value] = $offset;

            // ... put the offset value into the indexes ...
            foreach ($index_fields as $field) {
                $value = $record->$field;
                $this->index_fields[$field][$value][] = $offset;
            }

            // ... and increment the offset for the next record.
            $offset ++;
        }

        // return the list of field values in $data, and done
        return $return_values;
    }

    /**
     * 
     * Returns the array keys for the for the records in the IdentityMap;
     * the keys were generated at load() time from the identity field values.
     * 
     * @return array
     * 
     */
    public function getIdentityValues()
    {
        return array_keys($this->index_identity);
    }

    /**
     * 
     * Returns the values for a particular field for all the records in the
     * IdentityMap.
     * 
     * @param string $field The field name to get values for.
     * 
     * @return array An array of key-value pairs where the key is the identity
     * value and the value is the requested field value.
     * 
     */
    public function getFieldValues($field)
    {
        $values = [];
        $identity_field = $this->getIdentityField();
        foreach ($this->data as $offset => $record) {
            $identity_value = $record->$identity_field;
            $values[$identity_value] = $record->$field;
        }
        return $values;
    }

    /**
     * 
     * Retrieves a single record from the IdentityMap by the value of its
     * identity field.
     * 
     * @param int $identity_value The identity value of the record to be
     * retrieved.
     * 
     * @return object A record object via the record builder.
     * 
     */
    public function getRecord($identity_value)
    {
        // if the record is not in the identity index, exit early
        if (! isset($this->index_identity[$identity_value])) {
            return null;
        }

        // look up the sequential offset for the identity value
        $offset = $this->index_identity[$identity_value];
        return $this->offsetGet($offset);
    }

    /**
     * 
     * Retrieves the first record from the IdentityMap that matches the value
     * of an arbitrary field; it will be converted to a record object
     * if it is not already an object of the proper class.
     * 
     * N.b.: This will not be performant for large sets where the field is not
     * an identity field and is not indexed.
     * 
     * @param string $field The field to match on.
     * 
     * @param mixed $value The value of the field to match on.
     * 
     * @return object A record object via the record builder.
     * 
     */
    public function getRecordByField($field, $value)
    {
        // pre-emptively look for an identity field
        if ($field == $this->identity_field) {
            return $this->getRecord($value);
        }

        // pre-emptively look for an indexed field for that value
        if (isset($this->index_fields[$field])) {
            return $this->getRecordByIndex($field, $value);
        }

        // long slow loop through all the records to find a match.
        foreach ($this->data as $offset => $record) {
            if ($record->$field == $value) {
                return $this->offsetGet($offset);
            }
        }

        // no match!
        return null;
    }

    /**
     * 
     * Retrieves the first record from the IdentityMap matching an index 
     * lookup.
     * 
     * @param string $field The indexed field name.
     * 
     * @param string $value The field value to match on.
     * 
     * @return object A record object via the record builder.
     * 
     */
    protected function getRecordByIndex($field, $value)
    {
        if (! isset($this->index_fields[$field][$value])) {
            return null;
        }
        $offset = $this->index_fields[$field][$value][0];
        return $this->offsetGet($offset);
    }

    /**
     * 
     * Retrieves a collection of elements from the IdentityMap by the values
     * of their identity fields; each element will be converted to a record 
     * object if it is not already an object of the proper class.
     * 
     * @param array $identity_values An array of identity values to retrieve.
     * 
     * @return object A collection object via the collection builder.
     * 
     */
    public function getCollection(array $identity_values)
    {
        $list = [];
        foreach ($identity_values as $identity_value) {
            // look up the offset for the identity value
            $offset = $this->index_identity[$identity_value];
            // assigning by reference keeps the connections
            // when the element is converted to a record
            $list[] =& $this->data[$offset];
        }
        return $this->collection_builder->newInstance($list);
    }

    /**
     * 
     * Retrieves a collection of objects from the IdentityMap matching the 
     * value of an arbitrary field; these will be converted to records 
     * if they are not already objects of the proper class.
     * 
     * The value to be matched can be an array of values, so that you
     * can get many values of the field being matched.
     * 
     * If the field is indexed, the order of the returned collection
     * will match the order of the values being searched. If the field is not
     * indexed, the order of the returned collection will be the same as the 
     * IdentityMap.
     * 
     * The fastest results are from the identity field; second fastest, from
     * an indexed field; slowest are from non-indexed fields, because it has
     * to look through the entire IdentityMap to find matches.
     * 
     * @param string $field The field to match on.
     * 
     * @param mixed $values The value of the field to match on; if an array,
     * any value in the array will be counted as a match.
     * 
     * @return object A collection object via the collection builder.
     * 
     */
    public function getCollectionByField($field, $values)
    {
        $values = (array) $values;

        // pre-emptively look for an identity field
        if ($field == $this->identity_field) {
            return $this->getCollection($values);
        }

        // pre-emptively look for an indexed field
        if (isset($this->index_fields[$field])) {
            return $this->getCollectionByIndex($field, $values);
        }

        // long slow loop through all the records to find a match
        $list = [];
        foreach ($this->data as $identity_value => $record) {
            if (in_array($record->$field, $values)) {
                // assigning by reference keeps the connections
                // when the original is converted to a record
                $list[] =& $this->data[$identity_value];
            }
        }
        return $this->collection_builder->newInstance($list);
    }

    /**
     * 
     * Looks through the index for a field to retrieve a collection of
     * objects from the IdentityMap; these will be converted to records 
     * if they are not already objects of the proper class.
     * 
     * N.b.: The value to be matched can be an array of values, so that you
     * can get many values of the field being matched.
     * 
     * N.b.: The order of the returned collection will match the order of the
     * values being searched, not the order of the records in the IdentityMap.
     * 
     * @param string $field The field to match on.
     * 
     * @param mixed $values The value of the field to match on; if an array,
     * any value in the array will be counted as a match.
     * 
     * @return object A collection object via the collection builder.
     * 
     */
    protected function getCollectionByIndex($field, $values)
    {
        $values = (array) $values;
        $list = [];
        foreach ($values as $value) {
            // is there an index for that field value?
            if (isset($this->index_fields[$field][$value])) {
                // assigning by reference keeps the connections
                // when the original is converted to a record.
                foreach ($this->index_fields[$field][$value] as $offset) {
                    $list[] =& $this->data[$offset];
                }
            }
        }
        return $this->collection_builder->newInstance($list);
    }

    /**
     * 
     * Sets a relationship to another type, assigning it to a field
     * name to be used in record objects.
     * 
     * @param string $name The field name to use for the related record
     * or collection.
     * 
     * @param RelationInterface $relation The relationship definition object.
     * 
     * @return void
     * 
     */
    public function setRelation($name, RelationInterface $relation)
    {
        if (isset($this->relation[$name])) {
            throw new Exception("Relation '$name' already exists.");
        }
        $this->relation[$name] = $relation;
    }

    /**
     * 
     * Returns a relationship definition object by name.
     * 
     * @param string $name The field name to use for the related record
     * or collection.
     * 
     * @return AbstractRelation
     * 
     */
    public function getRelation($name)
    {
        return $this->relation[$name];
    }

    /**
     * 
     * Returns all the names of the relationship definition objects.
     * 
     * @return array
     * 
     */
    public function getRelationNames()
    {
        return array_keys($this->relation);
    }

    /**
     * 
     * Adds a new record to the IdentityMap.
     * 
     * This record will not show up in any indexes, whether by field or
     * by primary key. You will see it only by iterating through the
     * IdentityMap. Typically this is used to add to a collection, or
     * to create a new record from user input.
     * 
     * @param array $data Data for the new record.
     * 
     * @return object
     * 
     */
    public function newRecord(array $data = [])
    {
        $record = $this->record_builder->newInstance($this, $data);
        $this->index_new[] = count($this->data);
        $this->data[] = $record;
        return $record;
    }

    /**
     * 
     * Returns an array of all records in the IdentityMap that have been 
     * modified.
     * 
     * @return array
     * 
     */
    public function getChangedRecords()
    {
        $list = [];
        foreach ($this->index_identity as $identity_value => $offset) {
            $record = $this->data[$offset];
            if ($record->getChangedFields()) {
                $list[$identity_value] = $record;
            }
        }
        return $list;
    }

    /**
     * 
     * Returns an array of all records in the IdentityMap that were created
     * using `newRecord()`.
     * 
     * @return array
     * 
     */
    public function getNewRecords()
    {
        $list = [];
        foreach ($this->index_new as $offset) {
            $list[] = $this->data[$offset];
        }
        return $list;
    }
}
