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
namespace Aura\Marshal\Proxy;

/**
 * 
 * An interface for Proxy objects.
 * 
 * @package Aura.Marshal
 * 
 */
interface ProxyInterface
{
    /**
     * 
     * Gets a related foriegn entity or collection for a native entity.
     * 
     * @param object $entity The native entity.
     * 
     * @return object The related foreign entity or collection.
     * 
     */
    public function get($entity);
}
