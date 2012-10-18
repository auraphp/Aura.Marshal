<?php
namespace Aura\Marshal;

use Aura\Marshal\Relation\RelationInterface;

class Proxy
{
    protected $relation;
    
    public function __construct(RelationInterface $relation)
    {
        $this->relation = $relation;
    }
    
    public function get($record)
    {
        return $this->relation->getForRecord($record);
    }
}
