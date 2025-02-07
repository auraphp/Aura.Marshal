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
 * A builder for Lazy objects.
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
     * @var class-string<LazyInterface>
     *
     */
    protected $class = 'Aura\Marshal\Lazy\GenericLazy';

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
    public function newInstance(RelationInterface $relation)
    {
        $class = $this->class;
        return new $class($relation);
    }
}
