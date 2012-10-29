<?php
namespace Aura\Marshal\Record;

use Aura\Marshal\Proxy\ProxyInterface;

trait MagicPropertyTrait
{
    public function __get($field)
    {
        // get the property value
        $value = $this->$field;
        
        // is it a proxy for a related?
        if ($value instanceof ProxyInterface) {
            // replace the proxy value with the real value
            $value = $value->get($this);
            $this->$field = $value;
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
        return isset($this->$field);
    }
    
    public function __unset($field)
    {
        unset($this->$field);
    }
}
