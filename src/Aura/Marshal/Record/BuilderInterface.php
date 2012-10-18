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

/**
 * 
 * An inteface for RecordBuilder objects.
 * 
 * @package Aura.Marshal
 * 
 */
interface BuilderInterface
{
    /**
     * 
     * Creates a new record object.
     * 
     * @param mixed $data Data to load into the record.
     * 
     */
    public function newInstance(array $data);
}
