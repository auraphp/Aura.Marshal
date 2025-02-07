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

use Aura\Marshal\Collection\Builder as CollectionBuilder;
use Aura\Marshal\Exception;
use Aura\Marshal\Entity\Builder as EntityBuilder;
use Aura\Marshal\Lazy\Builder as LazyBuilder;

/**
 *
 * Builds a type object from an array of description information.
 *
 * @package Aura.Marshal
 *
 */
class Builder
{
    /**
     *
     * The class to use for new instances.
     *
     * @var class-string<GenericType>
     *
     */
    protected $class = 'Aura\Marshal\Type\GenericType';

    /**
     *
     * Returns a new type instance.
     *
     * The `$info` array should have four keys:
     *
     * - `'identity_field'` (string): The name of the identity field for
     *   entities of this type. This key is required.
     *
     * - `entity_builder` (Entity\BuilderInterface): A builder to create
     *   entity objects for the type. This key is optional, and defaults to a
     *   new Entity\Builder object.
     *
     * - `collection_builder` (Collection\BuilderInterface): A
     *   A builder to create collection objects for the type. This key
     *   is optional, and defaults to a new Collection\Builder object.
     *
     * @param array<string, mixed> $info An array of information about the type.
     *
     * @return GenericType
     *
     */
    public function newInstance($info)
    {
        $base = [
            'identity_field'        => null,
            'index_fields'          => [],
            'entity_builder'        => null,
            'collection_builder'    => null,
            'lazy_builder'          => null,
        ];

        $info = array_merge($base, $info);

        if (! $info['identity_field']) {
            throw new Exception('No identity field specified.');
        }

        if (! $info['entity_builder']) {
            $info['entity_builder'] = new EntityBuilder;
        }

        if (! $info['collection_builder']) {
            $info['collection_builder'] = new CollectionBuilder;
        }

        if (! $info['lazy_builder']) {
            $info['lazy_builder'] = new LazyBuilder;
        }

        $class = $this->class;
        $type = new $class;

        $type->setIdentityField($info['identity_field']);
        $type->setIndexFields($info['index_fields']);
        $type->setEntityBuilder($info['entity_builder']);
        $type->setCollectionBuilder($info['collection_builder']);
        $type->setLazyBuilder($info['lazy_builder']);

        return $type;
    }
}
