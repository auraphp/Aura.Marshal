<?php
namespace Aura\Marshal;

class Proxy
{
    protected $relation;
    
    public function __construct($relation)
    {
        $this->relation = $relation;
    }
    
    public function get($entity)
    {
        return $this->relation->getForEntity($entity);
    }
}
