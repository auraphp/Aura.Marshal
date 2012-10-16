<?php
namespace Aura\Marshal;

class Proxy
{
    protected $relation;
    
    public function __construct($relation)
    {
        $this->relation = $relation;
    }
    
    public function get($record)
    {
        return $this->relation->getForRecord($record);
    }
}
