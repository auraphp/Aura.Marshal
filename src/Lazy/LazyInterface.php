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
namespace Aura\Marshal\Lazy;

/**
 *
 * An interface for Lazy objects.
 *
 * @package Aura.Marshal
 *
 */
interface LazyInterface
{
    /**
     *
     * Gets a related foreign entity or collection for a native entity.
     *
     * @param object $entity The native entity.
     *
     * @return object The related foreign entity or collection.
     *
     */
    public function get($entity);
}
