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
namespace Aura\Marshal\Type;

use Aura\Marshal\Collection\BuilderInterface as CollectionBuilderInterface;
use Aura\Marshal\Collection\GenericCollection;
use Aura\Marshal\Data;
use Aura\Marshal\Exception;
use Aura\Marshal\Lazy\BuilderInterface as LazyBuilderInterface;
use Aura\Marshal\Entity\BuilderInterface as EntityBuilderInterface;
use Aura\Marshal\Entity\GenericEntity;
use Aura\Marshal\Relation\RelationInterface;
use SplObjectStorage;

/**
 *
 * Describes a particular type within the domain, and retains an IdentityMap
 * of entities for the type. Converts loaded data to entity objects lazily.
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
     * @var CollectionBuilderInterface
     *
     */
    protected $collection_builder;

    /**
     *
     * A builder to create entity objects for this type.
     *
     * @var EntityBuilderInterface
     *
     */
    protected $entity_builder;

    /**
     *
     * The entity field representing its unique identifier value. The
     * IdentityMap will be keyed on these values.
     *
     * @var string
     *
     */
    protected $identity_field;

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
     * @var array<string, array<string, int[]>>
     *
     */
    protected $index_fields = [];

    /**
     *
     * An index of entities on the identity field. The format is:
     *
     *      $index_identity[$identity_value] = $offset;
     *
     * Note that we always have only one offset, keyed by identity value.
     *
     * @var array<mixed, int>
     *
     */
    protected $index_identity = [];

    /**
     *
     * An index of all entities added via newEntity(). The format is:
     *
     *      $index_new[] = $offset;
     *
     * Note that we always have one offset, and the key is merely sequential.
     *
     * @var int[]
     *
     */
    protected $index_new = [];

    /**
     *
     * An array of all entities removed via `removeEntity()`.
     *
     * @var mixed[]
     *
     */
    protected $removed = [];

    /**
     *
     * An object store of the initial data for entities in the IdentityMap.
     *
     * @var SplObjectStorage<GenericEntity, array<string, mixed>>
     *
     */
    protected $initial_data;

    /**
     *
     * A builder to create Lazy objects.
     *
     * @var LazyBuilderInterface
     *
     */
    protected $lazy_builder;

    /**
     *
     * An array of relationship descriptions, where the key is a
     * field name for the entity and the value is a relation object.
     *
     * @var array<string, RelationInterface>
     *
     */
    protected $relations = [];

    /**
     *
     * Constructor; overrides the parent entirely.
     *
     * @param array<int|string, mixed> $data The initial data for all entities in the type.
     *
     */
    public function __construct(array $data = [])
    {
        $this->initial_data = new SplObjectStorage;
        $this->load($data);
    }

    /**
     *
     * Sets the name of the field that uniquely identifies each entity for
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
     * Returns the name of the field that uniquely identifies each entity of
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
     * @param string[] $fields The fields to be indexed.
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
     * @return string[]
     *
     */
    public function getIndexFields()
    {
        return array_keys($this->index_fields);
    }

    /**
     *
     * Sets the builder object to create entity objects.
     *
     * @param EntityBuilderInterface $entity_builder The builder object.
     *
     * @return void
     *
     */
    public function setEntityBuilder(EntityBuilderInterface $entity_builder)
    {
        $this->entity_builder = $entity_builder;
    }

    /**
     *
     * Returns the builder that creates entity objects.
     *
     * @return object
     *
     */
    public function getEntityBuilder()
    {
        return $this->entity_builder;
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
     * @return CollectionBuilderInterface
     *
     */
    public function getCollectionBuilder()
    {
        return $this->collection_builder;
    }

    /**
     *
     * Sets the lazy builder to create lazy objects.
     *
     * @param LazyBuilderInterface $lazy_builder The lazy builder.
     *
     * @return void
     *
     */
    public function setLazyBuilder(LazyBuilderInterface $lazy_builder)
    {
        $this->lazy_builder = $lazy_builder;
    }

    /**
     *
     * Returns the lazy builder that creates lazy objects.
     *
     * @return LazyBuilderInterface
     *
     */
    public function getLazyBuilder()
    {
        return $this->lazy_builder;
    }

    /**
     *
     * Loads the IdentityMap for this type with data for entity objects.
     *
     * Typically, the $data value is a sequential array of associative arrays.
     * As long as the $data value can be iterated over and accessed as an
     * array, you can pass in any kind of $data.
     *
     * The elements from $data will be placed into the IdentityMap and indexed
     * according to the value of their identity field.
     *
     * You can call load() multiple times, but entities already in the
     * IdentityMap will not be overwritten.
     *
     * The loaded elements are cast to objects; this allows consistent
     * addressing of elements before and after conversion to entity objects.
     *
     * The loaded elements will be converted to entity objects by the
     * entity builder only as you request them from the IdentityMap.
     *
     * @param array<int|string, mixed> $data Entity data to load into the IdentityMap.
     *
     * @param string $return_field Return values from this field; if empty,
     * return values from the identity field (the default).
     *
     * @return mixed[] The return values from the data elements, regardless
     * of whether they were loaded or not.
     *
     */
    public function load(array $data, $return_field = null)
    {
        // what is the identity field for the type?
        $identity_field = $this->getIdentityField();

        // what indexes do we need to track?
        $index_fields = array_keys($this->index_fields);

        // return a list of field values in $data
        $return_values = [];

        // what should the return field be?
        if (! $return_field) {
            $return_field = $identity_field;
        }

        // load each data element as a entity
        foreach ($data as $initial_data) {
            // cast the element to an object for consistent addressing
            $initial_data = $initial_data;
            // retain the return value on the entity
            $return_values[] = $initial_data[$return_field];
            // load into the map
            $this->loadData($initial_data, $identity_field, $index_fields);
        }

        // return the list of field values in $data, and done
        return $return_values;
    }

    /**
     *
     * Loads a single entity into the identity map.
     *
     * @param array<string, mixed> $initial_data The initial data for the entity.
     *
     * @return object The newly-loaded entity.
     *
     */
    public function loadEntity(array $initial_data)
    {
        // what is the identity field for the type?
        $identity_field = $this->getIdentityField();

        // what indexes do we need to track?
        $index_fields = array_keys($this->index_fields);

        // load the data and get the offset
        $offset = $this->loadData(
            $initial_data,
            $identity_field,
            $index_fields
        );

        // return the entity at the offset
        return $this->offsetGet($offset);
    }

    /**
     *
     * Loads an entity collection into the identity map.
     *
     * @param array<array<string, mixed>> $data The initial data for the entities.
     *
     * @return object The newly-loaded collection.
     *
     */
    public function loadCollection(array $data)
    {
        // what is the identity field for the type?
        $identity_field = $this->getIdentityField();

        // what indexes do we need to track?
        $index_fields = array_keys($this->index_fields);

        // the entities for the collection
        $entities = [];

        // load each new entity
        foreach ($data as $initial_data) {
            $offset = $this->loadData(
                $initial_data,
                $identity_field,
                $index_fields
            );
            $entity = $this->offsetGet($offset);
            $entities[] =& $entity;
        }

        // return a collection of the loaded entities
        return $this->collection_builder->newInstance($entities);
    }

    /**
     *
     * Loads an entity into the identity map.
     *
     * @param array<string, mixed> $initial_data The initial data for the entity.
     *
     * @param string $identity_field The identity field for the entity.
     *
     * @param string[] $index_fields The fields to index on.
     *
     * @return int The identity map offset of the new entity.
     *
     */
    protected function loadData(
        array $initial_data,
        $identity_field,
        array $index_fields
    ) {
        // does the identity already exist in the map?
        $identity_value = $initial_data[$identity_field];
        if (isset($this->index_identity[$identity_value])) {
            // yes; we're done, return the offset number
            return $this->index_identity[$identity_value];
        }

        // convert the initial data to a real entity in the identity map
        $this->data[] = $this->entity_builder->newInstance($initial_data);

        // get the entity and retain initial data
        $entity = end($this->data);
        $this->initial_data->attach($entity, $initial_data);

        // build indexes by offset
        $offset = key($this->data);
        $this->index_identity[$identity_value] = $offset;
        foreach ($index_fields as $field) {
            $value = $entity->$field;
            $this->index_fields[$field][$value][] = $offset;
        }

        // set related fields
        foreach ($this->getRelations() as $field => $relation) {
            $entity->$field = $this->lazy_builder->newInstance($relation);
        }

        // done! return the new offset number.
        return $offset;
    }

    /**
     *
     * Returns the array keys for the for the entities in the IdentityMap;
     * the keys were generated at load() time from the identity field values.
     *
     * @return mixed[]
     *
     */
    public function getIdentityValues()
    {
        return array_keys($this->index_identity);
    }

    /**
     *
     * Returns the values for a particular field for all the entities in the
     * IdentityMap.
     *
     * @param string $field The field name to get values for.
     *
     * @return array<mixed, mixed> An array of key-value pairs where the key is the identity
     * value and the value is the requested field value.
     *
     */
    public function getFieldValues($field)
    {
        $values = [];
        $identity_field = $this->getIdentityField();
        foreach ($this->data as $offset => $entity) {
            $identity_value = $entity->$identity_field;
            $values[$identity_value] = $entity->$field;
        }
        return $values;
    }

    /**
     *
     * Retrieves a single entity from the IdentityMap by the value of its
     * identity field.
     *
     * @param int $identity_value The identity value of the entity to be
     * retrieved.
     *
     * @return ?object A entity object via the entity builder.
     *
     */
    public function getEntity($identity_value)
    {
        // if the entity is not in the identity index, exit early
        if (! isset($this->index_identity[$identity_value])) {
            return null;
        }

        // look up the sequential offset for the identity value
        $offset = $this->index_identity[$identity_value];
        return $this->offsetGet($offset);
    }

    /**
     *
     * Retrieves the first entity from the IdentityMap that matches the value
     * of an arbitrary field; it will be converted to a entity object
     * if it is not already an object of the proper class.
     *
     * N.b.: This will not be performant for large sets where the field is not
     * an identity field and is not indexed.
     *
     * @param string $field The field to match on.
     *
     * @param mixed $value The value of the field to match on.
     *
     * @return ?object A entity object via the entity builder.
     *
     */
    public function getEntityByField($field, $value)
    {
        // pre-emptively look for an identity field
        if ($field == $this->identity_field) {
            return $this->getEntity($value);
        }

        // pre-emptively look for an indexed field for that value
        if (isset($this->index_fields[$field])) {
            return $this->getEntityByIndex($field, $value);
        }

        // long slow loop through all the entities to find a match.
        foreach ($this->data as $offset => $entity) {
            if ($entity->$field == $value) {
                return $this->offsetGet($offset);
            }
        }

        // no match!
        return null;
    }

    /**
     *
     * Retrieves the first entity from the IdentityMap matching an index
     * lookup.
     *
     * @param string $field The indexed field name.
     *
     * @param string $value The field value to match on.
     *
     * @return ?object A entity object via the entity builder.
     *
     */
    protected function getEntityByIndex($field, $value)
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
     * of their identity fields; each element will be converted to a entity
     * object if it is not already an object of the proper class.
     *
     * @param mixed[] $identity_values An array of identity values to retrieve.
     *
     * @return GenericCollection A collection object via the collection builder.
     *
     */
    public function getCollection(array $identity_values)
    {
        $list = [];
        foreach ($identity_values as $identity_value) {
            // look up the offset for the identity value
            $offset = $this->index_identity[$identity_value];
            // assigning by reference keeps the connections
            // when the element is converted to a entity
            $list[] =& $this->data[$offset];
        }
        return $this->collection_builder->newInstance($list);
    }

    /**
     *
     * Retrieves a collection of objects from the IdentityMap matching the
     * value of an arbitrary field; these will be converted to entities
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
     * @return GenericCollection A collection object via the collection builder.
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

        // long slow loop through all the entities to find a match
        $list = [];
        foreach ($this->data as $identity_value => $entity) {
            if (in_array($entity->$field, $values)) {
                // assigning by reference keeps the connections
                // when the original is converted to a entity
                $list[] =& $this->data[$identity_value];
            }
        }
        return $this->collection_builder->newInstance($list);
    }

    /**
     *
     * Looks through the index for a field to retrieve a collection of
     * objects from the IdentityMap; these will be converted to entities
     * if they are not already objects of the proper class.
     *
     * N.b.: The value to be matched can be an array of values, so that you
     * can get many values of the field being matched.
     *
     * N.b.: The order of the returned collection will match the order of the
     * values being searched, not the order of the entities in the IdentityMap.
     *
     * @param string $field The field to match on.
     *
     * @param mixed $values The value of the field to match on; if an array,
     * any value in the array will be counted as a match.
     *
     * @return GenericCollection A collection object via the collection builder.
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
                // when the original is converted to a entity.
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
     * name to be used in entity objects.
     *
     * @param string $name The field name to use for the related entity
     * or collection.
     *
     * @param RelationInterface $relation The relationship definition object.
     *
     * @return void
     *
     */
    public function setRelation($name, RelationInterface $relation)
    {
        if (isset($this->relations[$name])) {
            throw new Exception("Relation '$name' already exists.");
        }
        $this->relations[$name] = $relation;
    }

    /**
     *
     * Returns a relationship definition object by name.
     *
     * @param string $name The field name to use for the related entity
     * or collection.
     *
     * @return RelationInterface
     *
     */
    public function getRelation($name)
    {
        return $this->relations[$name];
    }

    /**
     *
     * Returns the array of all relationship definition objects.
     *
     * @return array<string, \Aura\Marshal\Relation\RelationInterface>
     *
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     *
     * Adds a new entity to the IdentityMap.
     *
     * This entity will not show up in any indexes, whether by field or
     * by primary key. You will see it only by iterating through the
     * IdentityMap. Typically this is used to add to a collection, or
     * to create a new entity from user input.
     *
     * @param array<int|string, mixed> $data Data for the new entity.
     *
     * @return GenericEntity
     *
     */
    public function newEntity(array $data = [])
    {
        $entity = $this->entity_builder->newInstance($data);
        $this->index_new[] = count($this->data);
        $this->data[] = $entity;
        return $entity;
    }

    /**
     *
     * Removes an entity from the collection.
     *
     * @param int $identity_value The identity value of the entity to be
     * removed.
     *
     * @return bool True on success, false on failure.
     *
     */
    public function removeEntity($identity_value)
    {
        // if the entity is not in the identity index, exit early
        if (! isset($this->index_identity[$identity_value])) {
            return false;
        }

        // look up the sequential offset for the identity value
        $offset = $this->index_identity[$identity_value];

        // get the entity
        $entity = $this->offsetGet($offset);

        // add the entity to the removed array
        $this->removed[$identity_value] = $entity;

        // remove the entity from the identity index
        unset($this->index_identity[$identity_value]);

        // get the index fields
        $index_fields = array_keys($this->index_fields);

        // loop through indices and remove offsets of this entity
        foreach ($index_fields as $field) {

            // get the field value
            $value = $entity->$field;

            /**
             * Find index of the offset with that value
             * 
             * @var int|false $offset_idx
             */
            $offset_idx = array_search(
                $offset,
                $this->index_fields[$field][$value]
            );

            // if the index exists, remove it, preserving index integrity
            if ($offset_idx !== false) {
                array_splice(
                    $this->index_fields[$field][$value],
                    $offset_idx,
                    1
                );
            }
        }

        // really remove the entity, and done
        $this->offsetUnset($offset);
        return true;
    }

    /**
     *
     * Returns an array of all entities in the IdentityMap that have been
     * modified.
     *
     * @return array<mixed, GenericEntity|mixed>
     *
     */
    public function getChangedEntities()
    {
        $list = [];
        foreach ($this->index_identity as $identity_value => $offset) {
            $entity = $this->data[$offset];
            if ($this->getChangedFields($entity)) {
                $list[$identity_value] = $entity;
            }
        }
        return $list;
    }

    /**
     *
     * Returns an array of all entities in the IdentityMap that were created
     * using `newEntity()`.
     *
     * @return array<GenericEntity|mixed>
     *
     */
    public function getNewEntities()
    {
        $list = [];
        foreach ($this->index_new as $offset) {
            $list[] = $this->data[$offset];
        }
        return $list;
    }

    /**
     *
     * Returns all non-removed entities in the type.
     *
     * @return array<int|string, \Aura\Marshal\GenericEntity|mixed>
     *
     */
    public function getAllEntities()
    {
        return $this->data;
    }

    /**
     *
     * Returns an array of all entities that were removed using
     * `removeEntity()`.
     *
     * @return mixed[]
     *
     */
    public function getRemovedEntities()
    {
        return $this->removed;
    }

    /**
     *
     * Returns the initial data for a given entity.
     *
     * @param GenericEntity $entity The entity to find initial data for.
     *
     * @return null|array<string, mixed> The initial data for the entity.
     *
     */
    public function getInitialData($entity)
    {
        if ($this->initial_data->contains($entity)) {
            return $this->initial_data[$entity];
        }

        return null;
    }

    /**
     *
     * Returns the changed fields and their values for an entity.
     *
     * @param GenericEntity $entity The entity to find changes for.
     *
     * @return array<string, mixed> An array of key-value pairs where the key is the field
     * name and the value is the changed value.
     *
     */
    public function getChangedFields($entity)
    {
        // the eventual list of changed fields and values
        $changed = [];

        // initial data for this entity
        $initial_data = $this->getInitialData($entity) ?? [];

        // go through all the initial data values
        foreach ($initial_data as $field => $old) {

            // what is the new value on the entity?
            $new = $entity->$field;

            // are both old and new values numeric?
            $numeric = is_numeric($old) && is_numeric($new);

            // if both old and new are numeric, compare loosely.
            if ($numeric && $old != $new) {
                // loosely different, retain the new value
                $changed[$field] = $new;
            }

            // if one or the other is not numeric, compare strictly
            if (! $numeric && $old !== $new) {
                // strictly different, retain the new value
                $changed[$field] = $new;
            }
        }

        // done!
        return $changed;
    }

    /**
     *
     * Unsets all entities from this type.
     *
     * @return void
     *
     */
    public function clear()
    {
        $this->data = [];
        $this->index_identity = [];
        $this->index_new = [];
        $this->removed = [];
        $this->initial_data = new SplObjectStorage;
    }
}
