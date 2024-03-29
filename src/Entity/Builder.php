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
namespace Aura\Marshal\Entity;

use Aura\Marshal\Lazy\BuilderInterface as LazyBuilderInterface;

/**
 *
 * Creates a new entity object for a type.
 *
 * @package Aura.Marshal
 *
 */
class Builder implements BuilderInterface
{
    /**
     *
     * The class to use for new instances.
     *
     * @var string
     *
     */
    protected $class = 'Aura\Marshal\Entity\GenericEntity';

    /**
     *
     * Creates a new entity object.
     *
     * @param array|object $data Data to load into the entity.
     *
     * @return GenericEntity
     *
     */
    public function newInstance(array $data)
    {
        $class = $this->class;
        $entity = new $class;

        // set fields
        foreach ($data as $field => $value) {
            $entity->$field = $value;
        }

        return $entity;
    }
}
