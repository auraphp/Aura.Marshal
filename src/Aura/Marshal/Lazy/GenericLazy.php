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
namespace Aura\Marshal\Lazy;

use Aura\Marshal\Relation\RelationInterface;

/**
 * 
 * A generic Lazy object, useful in cases when special functionality is not
 * needed.
 * 
 * @package Aura.Marshal
 * 
 */
class GenericLazy implements LazyInterface
{
    /**
     * 
     * A Relation object between native and foreign types.
     * 
     * @var RelationInterface
     * 
     */
    protected $relation;
    
    /**
     * 
     * Constructor.
     * 
     * @param RelationInterface $relation A relation between native and
     * foreign types.
     * 
     */
    public function __construct(RelationInterface $relation)
    {
        $this->relation = $relation;
    }
    
    /**
     * 
     * Gets a related foriegn entity or collection for a native entity.
     * 
     * @param object $entity The native entity.
     * 
     * @return object The related foreign entity or collection.
     * 
     */
    public function get($entity)
    {
        return $this->relation->getForEntity($entity);
    }
}
