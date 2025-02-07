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
namespace Aura\Marshal\Collection;

/**
 *
 * An interface for collection builder objects.
 *
 * @package Aura.Marshal
 *
 */
interface BuilderInterface
{
    /**
     *
     * Creates a new collection object.
     *
     * @param array<int|string, mixed> $data Data to load into the collection.
     * 
     * @return GenericCollection
     *
     */
    public function newInstance(array $data);
}
