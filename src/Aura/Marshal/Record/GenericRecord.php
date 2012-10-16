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

use Aura\Marshal\Data;

/**
 * 
 * Represents a single record.
 * 
 * @package Aura.Marshal
 * 
 */
class GenericRecord extends Data
{
    use MagicArrayAccessTrait;
}
