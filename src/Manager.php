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
namespace Aura\Marshal;

use Aura\Marshal\Type\Builder as TypeBuilder;
use Aura\Marshal\Relation\Builder as RelationBuilder;
use Aura\Marshal\Type\GenericType;

/**
 *
 * A manager for the types in the domain model.
 *
 * @package Aura.Marshal
 *
 */
class Manager
{
    /**
     *
     * A builder for type objects.
     *
     * @var TypeBuilder
     *
     */
    protected $type_builder;

    /**
     *
     * A builder for relation objects.
     *
     * @var RelationBuilder
     *
     */
    protected $relation_builder;

    /**
     *
     * An array of type definition arrays, which are converted to type
     * objects as you request them.
     *
     * @var array<string, GenericType|array<string, mixed>>
     *
     */
    protected $types;

    /**
     *
     * Constructor.
     *
     * The $types definition array looks like this:
     *
     *      $types = [
     *
     *          // the name to use for this type in the manager
     *          'some_type_name' => [
     *
     *              // the identity field in each entity
     *              'identity_field' => 'id',
     *
     *              // fields to index against
     *              'index_fields' => ['field1', 'field2'],
     *
     *              // a entity builder for the type
     *              'entity_builder' => new \Aura\Domain\EntityBuilder,
     *
     *              // a collection builder for the type
     *              'collection_builder' => new \Aura\Domain\CollectionBuilder,
     *
     *              // relationship definitions
     *              'relation_names' => [
     *                  // discussed below
     *              ],
     *          ),
     *
     *          'next_type_name' => [
     *              // ...
     *          ],
     *
     *          // ...
     *      );
     *
     * The relationship definitions portion looks like this:
     *
     *      $relation_names = [
     *
     *          'name_for_relation_1' => [
     *
     *              // the relationship to the native (parent) type: the parent
     *              // belongs_to, has_one, has_many, or has_many_through
     *              // of the foreign type. required.
     *              'relationship' => 'has_many',
     *
     *              // the name of the foreign (related) type in the manager.
     *              // optional; by default, uses the relation name as the
     *              // foreign type.
     *              'foreign_type' => 'another_type_name',
     *
     *              // the name of the native (parent) entity field to use
     *              // when matching foreign (related) entities. required.
     *              'native_field' => 'native_field_name',
     *
     *              // the name of the foreign (related) entity field to use
     *              // when matching the native (parent) entity. required.
     *              'foreign_field' => 'foreign_field_name',
     *
     *              // -------------------------------------------------------
     *              // if you have a has_many_through relationship, add the
     *              // following three keys to set up the association mapping.
     *
     *              // the name of the type through which the native
     *              // and foreign types are mapped to each other.
     *              'through_type' => 'mapping_type_name',
     *
     *              // in the "through" entity, the name of the field that
     *              // maps to the 'native_field' value
     *              'through_native_field' => 'mapping_native_field_name',
     *
     *              // in the "through" entity, the name of the field that
     *              // maps to the 'foreign_field' value
     *              'through_foreign_field' => 'mapping_foreign_field_name',
     *          ),
     *
     *          'name_for_relation_2' => [
     *              // ...
     *          ],
     *
     *          // ...
     *      );
     *
     * @param TypeBuilder $type_builder A builder for type objects.
     *
     * @param RelationBuilder $relation_builder A builder for relation objects.
     *
     * @param array<string, array<string, mixed>> $types Type definitions.
     *
     */
    public function __construct(
        TypeBuilder $type_builder,
        RelationBuilder $relation_builder,
        array $types = []
    ) {
        $this->type_builder     = $type_builder;
        $this->relation_builder = $relation_builder;
        $this->types            = $types;
    }

    /**
     *
     * Sets one type in the manager.
     *
     * @param string $name The name to use for the type.
     *
     * @param array<string, mixed> $info An array of type definition information.
     *
     * @return void
     *
     */
    public function setType($name, array $info)
    {
        if (isset($this->types[$name])) {
            throw new Exception("Type '$name' is already in the manager.");
        }

        $this->types[$name] = $info;
    }

    /**
     *
     * Sets a one relation for a type in the manager.
     *
     * @param string $type The type to set the relation on.
     *
     * @param string $name The name for the relation.
     *
     * @param array<string, mixed> $info The relation information.
     *
     * @return void
     *
     */
    public function setRelation($type, $name, $info)
    {
        if (! isset($this->types[$type])) {
            throw new Exception("Type '$type' is not in the manager.");
        }

        if ($this->types[$type] instanceof GenericType) {
            // set on a type instance
            $relation = $this->relation_builder->newInstance(
                $type,
                $name,
                $info,
                $this
            );
            $this->types[$type]->setRelation($name, $relation);
        } else {
            // set the relation name on a type definition
            $this->types[$type]['relation_names'][$name] = $info;
        }
    }

    /**
     *
     * Gets a type by name, creating a type object for it as needed.
     *
     * @param string $name The type name to retrieve.
     *
     * @return GenericType
     *
     */
    public function __get($name)
    {
        if (! isset($this->types[$name])) {
            throw new Exception("Type '$name' not in the manager.");
        }

        if (! $this->types[$name] instanceof GenericType) {
            $this->buildType($name);
        }

        /** @var GenericType $type */
        $type = $this->types[$name];

        return $type;
    }

    /**
     *
     * Builds a type object from a type definition.
     *
     * The build process happens in two stages:
     *
     * 1. Use the $type_builder to create the type object.
     *
     * 2. Add relationships from the type definition.
     *
     * The two-stage process helps avoid race conditions where a type may
     * have relationships to other type object that might not be in the
     * manager yet.
     *
     * @param string $name The type name to build.
     *
     * @return void
     *
     */
    protected function buildType($name)
    {
        /**
         * Instantiate and retain the type object. if we don't do this before
         * building related fields, then we enter a race condition.
         * 
         * @var array<string, mixed> $info
         */
        $info = $this->types[$name];
        $this->types[$name] = $this->type_builder->newInstance($info);

        // add the related fields to the type
        if (isset($info['relation_names'])) {
            foreach ($info['relation_names'] as $relname => $relinfo) {
                $this->setRelation($name, $relname, $relinfo);
            }
        }
    }

    /**
     *
     * Returns the names of all types in the manager.
     *
     * @return string[]
     *
     */
    public function getTypes()
    {
        return array_keys($this->types);
    }

    /**
     *
     * Unsets all entities in all types in the manager.
     *
     * @return void
     *
     */
    public function clear()
    {
        foreach ($this->types as $type) {
            if ($type instanceof GenericType) {
                $type->clear();
            }
        }
    }
}
