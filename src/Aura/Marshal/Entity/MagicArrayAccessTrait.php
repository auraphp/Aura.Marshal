<?php
namespace Aura\Marshal\Entity;

use Aura\Marshal\Proxy\ProxyInterface;

trait MagicArrayAccessTrait
{
    public function __get($field)
    {
        // get the field value
        $value = $this->offsetGet($field);
        
        // is it a proxy for a related?
        if ($value instanceof ProxyInterface) {
            // replace the proxy value with the real value
            $value = $value->get($this);
            $this->offsetSet($field, $value);
        }
        
        // done!
        return $value;
    }
    
    public function __set($field, $value)
    {
        $this->offsetSet($field, $value);
    }
    
    public function __isset($field)
    {
        return $this->offsetExists($field);
    }
    
    public function __unset($field)
    {
        $this->offsetUnset($field);
    }
}
