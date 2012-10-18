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
     * @var array
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
     * @param string $type The name of the native type.
     * 
     * @param string $name The name of the record field where the related
     * data will be placed.
     * 
     * @param array $info An array of relationship definition information.
     * 
     * @param Manager $manager An type manager.
     * 
     * @return AbstractRelation
     * 
     */
    public function newInstance($type, $name, $info, Manager $manager)
    {
        $base = [
            'foreign_type'          => $name,
            'relationship'          => null,
            'native_field'          => null,
            'foreign_field'         => null,
            'through_type'          => null,
            'through_native_field'  => null,
            'through_foreign_field' => null,
        ];

        $info = array_merge($base, $info);

        $relationship = $info['relationship'];
        unset($info['relationship']);

        if (! $relationship) {
            throw new Exception("No 'relationship' specified for relation '$name' in type '$type'.");
        }

        $class = $this->relationship_class[$relationship];
        $relation = new $class($type, $name, $info, $manager);

        return $relation;
    }
}
