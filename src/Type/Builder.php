<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Marshal\Type;
use Aura\Marshal\Collection\Builder as CollectionBuilder;
use Aura\Marshal\Exception;
use Aura\Marshal\Record\Builder as RecordBuilder;

/**
 * 
 * Builds a type object from an array of description information.
 * 
 * @package Aura.Marshal
 * 
 */
class Builder
{
    /**
     * 
     * Returns a new type instance.
     * 
     * The `$info` array should have four keys:
     * 
     * - `'identity_field'` (string): The name of the identity field for 
     *   records of this type. This key is required.
     * 
     * - `record_class` (string): The name of the record class returned by the
     *   record Builder. This key is optional, and defaults to 
     *   `Aura\Marshal\Record`.
     * 
     * - `record_builder` (Record\BuilderInterface): A builder to create
     *   record objects for the type. This key is optional, and defaults to a
     *   new Record\Builder object.
     * 
     * - `collection_builder` (Collection\BuilderInterface): A 
     *   A builder to create collection objects for the type. This key
     *   is optional, and defaults to a new Collection\Builder object.
     * 
     * @param array $info An array of information about the type.
     * 
     * @return GenericType
     * 
     */
    public function newInstance($info)
    {
        $base = array(
            'identity_field'        => null,
            'record_class'          => 'Aura\Marshal\Record\GenericRecord',
            'record_builder'        => null,
            'collection_builder'    => null,
        );
        
        $info = array_merge($base, $info);
        
        if (! $info['identity_field']) {
            throw new Exception('No identity field specified.');
        }
        
        if (! $info['record_builder']) {
            $info['record_builder'] = new RecordBuilder;
        }
        
        if (! $info['collection_builder']) {
            $info['collection_builder'] = new CollectionBuilder;
        }
        
        $type = new GenericType;
        $type->setIdentityField($info['identity_field']);
        $type->setRecordClass($info['record_class']);
        $type->setRecordBuilder($info['record_builder']);
        $type->setCollectionBuilder($info['collection_builder']);
        
        return $type;
    }
}
