<?php
namespace Aura\Marshal;

use Aura\Marshal\Relation\RelationInterface;

class MockRelation implements RelationInterface
{
    public function getForEntity($entity)
    {
        return (object) ['foreign_field' => 'foreign_value'];
    }
}
