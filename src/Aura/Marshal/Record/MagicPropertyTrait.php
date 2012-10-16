<?php
namespace Aura\Marshal\Record;

use Aura\Marshal\Proxy;

trait MagicPropertyTrait
{
    public function __get($field)
    {
        // get the property value
        $value = $this->$field;
        
        // is it a proxy for a related?
        if ($value instanceof Proxy) {
            // replace the proxy value with the real value
            $value = $value->get($this);
            $this->$field = $value->get($this);
        }
        
        // done!
        return $value;
    }
    
    public function __set($field, $value)
    {
        $this->$field = $value;
    }
    
    public function __isset($field)
    {
        return isset($field);
    }
    
    public function __unset($field)
    {
        unset($this->$field);
    }
}
