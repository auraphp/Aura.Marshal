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

use Aura\Marshal\Exception;
use Aura\Marshal\Manager;

/**
 *
 * A builder to create relationship definition objects.
 *
 * @package Aura.Marshal
 *
 */
class Builder
{
    /**
     *
     * A map of relationships to classes.
     *
     * @var array<string, class-string<RelationInterface>>
     *
     */
    protected $relationship_class = [
        'belongs_to'       => 'Aura\Marshal\Relation\BelongsTo',
        'has_one'          => 'Aura\Marshal\Relation\HasOne',
        'has_many'         => 'Aura\Marshal\Relation\HasMany',
        'has_many_through' => 'Aura\Marshal\Relation\HasManyThrough',
    ];

    /**
     *
     * Builds and returns a relation object.
     *
     * @param string $type The name of the native type in the manager.
     *
     * @param string $name The name of the native field for the related
     * entity or collection.
     *
     * @param array<string, mixed> $info An array of relationship definition information.
     *
     * @param Manager $manager A type manager.
     *
     * @return RelationInterface
     *
     */
    public function newInstance($type, $name, array $info, Manager $manager)
    {
        $base = [
            'relationship'          => null,
            'native_field'          => null,
            'foreign_type'          => $name,
            'foreign_field'         => null,
            'through_type'          => null,
            'through_native_field'  => null,
            'through_foreign_field' => null,
        ];

        $info = array_merge($base, $info);
        $info['type'] = $type;
        $info['name'] = $name;

        $this->prepRelationship($info, $manager);
        $this->prepNative($info, $manager);
        $this->prepForeign($info, $manager);
        $this->prepThrough($info, $manager);

        $relationship = $info['relationship'];
        $class = $this->relationship_class[$relationship];

        return new $class(
            $info['native_field'],
            $info['foreign'],
            $info['foreign_type'],
            $info['foreign_field'],
            $info['through'],
            $info['through_type'],
            $info['through_native_field'],
            $info['through_foreign_field']
        );
    }

    /**
     *
     * Prepares the type-of-relationship name.
     *
     * @param array<string, mixed> $info The relationship definition.
     *
     * @param Manager $manager The type manager.
     *
     * @return void
     *
     */
    protected function prepRelationship(&$info, Manager $manager)
    {
        if (! $info['relationship']) {
            throw new Exception("No 'relationship' specified for relation '{$info['name']}' on type '{$info['type']}'.");
        }
    }

    /**
     *
     * Prepares the native field name.
     *
     * @param array<string, mixed> $info The relationship definition.
     *
     * @param Manager $manager The type manager.
     *
     * @return void
     *
     */
    protected function prepNative(&$info, Manager $manager)
    {
        if (! $info['native_field']) {
            throw new Exception("No 'native_field' specified for relation '{$info['name']}' on type '{$info['type']}'.");
        }
    }

    /**
     *
     * Prepares the foreign type name, field name, and type object.
     *
     * @param array<string, mixed> $info The relationship definition.
     *
     * @param Manager $manager The type manager.
     *
     * @return void
     *
     */
    protected function prepForeign(&$info, Manager $manager)
    {
        if (! $info['foreign_type']) {
            throw new Exception("No 'foreign_type' specified for relation '{$info['name']}' on type '{$info['type']}'.");
        }

        if (! $info['foreign_field']) {
            throw new Exception("No 'foreign_field' specified for relation '{$info['name']}' on type '{$info['type']}'.");
        }

        $info['foreign'] = $manager->__get($info['foreign_type']);
    }

    /**
     *
     * Prepares the through type name, field names, and type object.
     *
     * @param array<string, mixed> $info The relationship definition.
     *
     * @param Manager $manager The type manager.
     *
     * @return void
     *
     */
    protected function prepThrough(&$info, Manager $manager)
    {
        if ($info['relationship'] != 'has_many_through') {
            $info['through'] = null;
            return;
        }

        if (! $info['through_type']) {
            throw new Exception("No 'through_type' specified for relation '{$info['name']}' on type '{$info['type']}'.");
        }

        if (! $info['through_native_field']) {
            throw new Exception("No 'through_native_field' specified for relation '{$info['name']}' on type '{$info['type']}'.");
        }

        if (! $info['through_foreign_field']) {
            throw new Exception("No 'through_foreign_field' specified for relation '{$info['name']}' on type '{$info['type']}'.");
        }

        $info['through'] = $manager->__get($info['through_type']);
    }
}
