<?php
namespace Aura\Marshal\Proxy;

use Aura\Marshal\Relation\RelationInterface;

class Builder implements BuilderInterface
{
    public function newInstance(RelationInterface $relation)
    {
        return new GenericProxy($relation);
    }
}
