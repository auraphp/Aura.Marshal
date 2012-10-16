<?php
namespace Aura\Marshal;

use Aura\Marshal\Relation\AbstractRelation;

class ProxyBuilder
{
    public function newInstance(AbstractRelation $relation)
    {
        return new Proxy($relation);
    }
}
