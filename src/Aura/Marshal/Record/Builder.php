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
use Aura\Marshal\ProxyBuilder;

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
    
    protected $proxy_builder;
    
    public function __construct(ProxyBuilder $proxy_builder)
    {
        $this->proxy_builder = $proxy_builder;
    }
    
    /**
     * 
     * Creates a new record object.
     * 
     * @param GenericType $type The type for this record.
     * 
     * @param array|object $data Data to load into the record.
     * 
     * @return GenericRecord
     * 
     */
    public function newInstance(GenericType $type, $data)
    {
        $class = $this->class;
        $record = new $class([], $type);
        foreach ((array) $data as $field => $value) {
            $record->$field = $value;
        }
        return $record;
    }
}
