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
namespace Aura\Marshal\Relation;

use Aura\Marshal\Exception;
use Aura\Marshal\Manager;
use Aura\Marshal\Type\GenericType;

/**
 * 
 * Represents a relationship definition between two types.
 * 
 * @package Aura.Marshal
 * 
 */
abstract class AbstractRelation
{
    /**
     * 
     * The type manager object.
     * 
     * @var Manager
     * 
     */
    protected $manager;

    /**
     * 
     * The foreign type object.
     * 
     * @var string
     * 
     */
    protected $foreign;
    
    /**
     * 
     * The name of the foreign type.
     * 
     * @var string
     * 
     */
    protected $foreign_type;

    /**
     * 
     * The field in the native record to match against.
     * 
     * @var string
     * 
     */
    protected $native_field;

    /**
     * 
     * The field in the foreign record to match against.
     * 
     * @var string
     * 
     */
    protected $foreign_field;

    /**
     * 
     * Constructor.
     * 
     * @param string $type The name of the native type.
     * 
     * @param string $name The name of the record field where the related
     * data will be placed.
     * 
     * @param array $info An array of relationship definition information.
     * 
     * @param Manager $manager The type manager.
     * 
     */
    public function __construct($type, $name, $info, Manager $manager)
    {
        if (! $info['foreign_type']) {
            throw new Exception("No 'foreign_type' specified for relation '$name' in type '$type'.");
        }

        if (! $info['native_field']) {
            throw new Exception("No 'native_field' specified for relation '$name' in type '$type'.");
        }

        if (! $info['foreign_field']) {
            throw new Exception("No 'foreign_field' specified for relation '$name' in type '$type'.");
        }

        $this->manager       = $manager;
        $this->foreign       = $this->manager->__get($info['foreign_type']);
        $this->foreign_type  = $info['foreign_type'];
        $this->native_field  = $info['native_field'];
        $this->foreign_field = $info['foreign_field'];
    }
    
    public function getForeignType()
    {
        return $this->foreign_type;
    }
}
