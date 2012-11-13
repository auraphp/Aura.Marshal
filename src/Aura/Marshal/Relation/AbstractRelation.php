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
     * The field in the native entity to match against.
     * 
     * @var string
     * 
     */
    protected $native_field;

    /**
     * 
     * The foreign type object.
     * 
     * @var GenericType
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
     * The field in the foreign entity to match against.
     * 
     * @var string
     * 
     */
    protected $foreign_field;

    /**
     * 
     * The through type object.
     * 
     * @var GenericType
     * 
     */
    protected $through;
    
    /**
     * 
     * Native and foreign entities are mapped to each other through this
     * association type.
     * 
     * @var string
     * 
     */
    protected $through_type;

    /**
     * 
     * The field name for the native side of the association mapping in the
     * "through" type.
     * 
     * @var string
     * 
     */
    protected $through_native_field;

    /**
     * 
     * The field name for the foreign side of the association mapping in the
     * "through" type.
     * 
     * @var string
     * 
     */
    protected $through_foreign_field;

    /**
     * 
     * Constructor.
     * 
     * @param string $type The name of the native type.
     * 
     * @param string $name The name of the entity field where the related
     * data will be placed.
     * 
     * @param array $info An array of relationship definition information.
     * 
     */
    public function __construct(
        $native_field,
        GenericType $foreign,
        $foreign_type,
        $foreign_field,
        GenericType $through = null,
        $through_type = null,
        $through_native_field = null,
        $through_foreign_field = null
    ) {
        $this->native_field          = $native_field;
        $this->foreign               = $foreign;
        $this->foreign_type          = $foreign_type;
        $this->foreign_field         = $foreign_field;
        $this->through               = $through;
        $this->through_type          = $through_type;
        $this->through_native_field  = $through_native_field;
        $this->through_foreign_field = $through_foreign_field;
    }
    
    public function getForeignType()
    {
        return $this->foreign_type;
    }
}
