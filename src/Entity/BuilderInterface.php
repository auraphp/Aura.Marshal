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

/**
 *
 * An interface for EntityBuilder objects.
 *
 * @package Aura.Marshal
 *
 */
interface BuilderInterface
{
    /**
     *
     * Creates a new entity object.
     *
     * @param array<int|string, mixed> $data Data to load into the entity.
     * 
     * @return GenericEntity
     *
     */
    public function newInstance(array $data);
}
