<?php
namespace Aura\Marshal\Proxy;

use Aura\Marshal\Relation\RelationInterface;

interface BuilderInterface
{
    public function newInstance(RelationInterface $relation);
}
