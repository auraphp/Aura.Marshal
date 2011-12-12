<?php
/**
 * 
 * This file is part of the Aura project for PHP.
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
 * Represents a relationship where a native record has many foreign records
 * (i.e., a foreign collection) mapped through an association type.
 * 
 * @package Aura.Marshal
 * 
 */
class HasManyThrough extends AbstractRelation implements RelationInterface
{
    /**
     * 
     * Native and foreign records are mapped to each other through this
     * association type.
     * 
     * @var GenericType
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
     * @param array $info An array of relationship definition information.
     * 
     * @param Manager $manager The type manager.
     * 
     */
    public function __construct($type, $name, $info, Manager $manager)
    {
        parent::__construct($type, $name, $info, $manager);
        
        if (! $info['through_type']) {
            throw new Exception("No 'through_type' specified for relation '$name' in type '$type'.");
        }
        
        if (! $info['through_native_field']) {
            throw new Exception("No 'through_native_field' specified for relation '$name' in type '$type'.");
        }
        
        if (! $info['through_foreign_field']) {
            throw new Exception("No 'through_foreign_field' specified for relation '$name' in type '$type'.");
        }
        
        $this->through_type          = $this->manager->{$info['through_type']};
        $this->through_native_field  = $info['through_native_field'];
        $this->through_foreign_field = $info['through_foreign_field'];
    }
    
    /**
     * 
     * Returns the related foreign collection for a native record.
     * 
     * @param mixed $record The native record.
     * 
     * @return GenericCollection
     * 
     */
    public function getForRecord($record)
    {
        // first, find the native values in the through type
        $native_field = $this->native_field;
        $native_value = $record->$native_field;
        $through_coll = $this->through_type->getCollectionByField(
            $this->through_native_field,
            $native_value
        );
        
        // now find the foreign values from the through collection
        $foreign_values = $through_coll->getFieldValues(
            $this->through_foreign_field
        );
        
        // finally, return a foreign collection based on the foreign values
        return $this->foreign_type->getCollectionByField(
            $this->foreign_field,
            $foreign_values
        );
    }
}
