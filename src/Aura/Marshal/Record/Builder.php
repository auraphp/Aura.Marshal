<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Marshal
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Marshal\Record;

use Aura\Marshal\Type\GenericType;
use Aura\Marshal\Proxy\BuilderInterface as ProxyBuilderInterface;

/**
 * 
 * Creates a new record object for a type.
 * 
 * @package Aura.Marshal
 * 
 */
class Builder implements BuilderInterface
{
    protected $class = 'Aura\Marshal\Record\GenericRecord';
    
    /**
     * 
     * Creates a new record object.
     * 
     * @param array|object $data Data to load into the record.
     * 
     * @return GenericRecord
     * 
     */
    public function newInstance(array $data)
    {
        $class = $this->class;
        $record = new $class;
        
        // set fields
        foreach ($data as $field => $value) {
            $record->$field = $value;
        }
        
        return $record;
    }
}
