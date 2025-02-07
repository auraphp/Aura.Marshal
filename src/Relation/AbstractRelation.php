<?php
/**
 *
 * This file is part of the Aura project for PHP.
 *
 * @package Aura.Marshal
 *
 * @license https://opensource.org/licenses/mit-license.php MIT
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
     * @var ?GenericType
     *
     */
    protected $through;

    /**
     *
     * Native and foreign entities are mapped to each other through this
     * association type.
     *
     * @var ?string
     *
     */
    protected $through_type;

    /**
     *
     * The field name for the native side of the association mapping in the
     * "through" type.
     *
     * @var ?string
     *
     */
    protected $through_native_field;

    /**
     *
     * The field name for the foreign side of the association mapping in the
     * "through" type.
     *
     * @var ?string
     *
     */
    protected $through_foreign_field;

    /**
     *
     * Constructor.
     *
     * @param string $native_field The name of the native field.
     *
     * @param GenericType $foreign The foreign type object.
     *
     * @param string $foreign_type The manager name of the foreign type.
     *
     * @param string $foreign_field The name of the foreign field.
     *
     * @param GenericType $through The through type object.
     *
     * @param ?string $through_type The manager name of the through type.
     *
     * @param ?string $through_native_field The name of the native field in
     * the through type.
     *
     * @param ?string $through_foreign_field The name of the foreign field in
     * the through type.
     *
     */
    public function __construct(
        $native_field,
        GenericType $foreign,
        $foreign_type,
        $foreign_field,
        ?GenericType $through = null,
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

    /**
     *
     * Gets the name of the foreign type in the manager.
     *
     * @return string
     *
     */
    public function getForeignType()
    {
        return $this->foreign_type;
    }
}
