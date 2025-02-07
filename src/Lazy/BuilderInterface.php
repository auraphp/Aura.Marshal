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

use Aura\Marshal\Relation\RelationInterface;

/**
 *
 * An interface for Lazy builders.
 *
 * @package Aura.Marshal
 *
 */
interface BuilderInterface
{
    /**
     *
     * Creates a new Lazy object.
     *
     * @param RelationInterface $relation The relationship object between the
     * native and foreign types.
     *
     * @return LazyInterface
     *
     */
    public function newInstance(RelationInterface $relation);
}
