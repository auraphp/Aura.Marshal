<?php
namespace Aura\Marshal\Proxy;

use Aura\Marshal\Relation\RelationInterface;

class Builder implements BuilderInterface
{
    protected $class = 'Aura\Marshal\Proxy\GenericProxy';
    
    public function newInstance(RelationInterface $relation)
    {
        $class = $this->class;
        return new $class($relation);
    }
}
