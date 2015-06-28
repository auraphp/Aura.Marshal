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
namespace Aura\Marshal\Entity;

use Aura\Marshal\Lazy\LazyInterface;

/**
 *
 * Creates a new entity object for a type.
 *
 * @package Aura.Marshal
 *
 */
class Builder implements BuilderInterface
{
    /**
     *
     * The class to use for new instances.
     *
     * @var string
     *
     */
    protected $class = 'stdClass';

    /**
     *
     * Creates a new entity object.
     *
     * @param array|object $data Data to load into the entity.
     *
     * @return object
     *
     */
    public function newInstance(array $data)
    {
        if ($this->hasMagicTrait()) {
            return $this->buildMagicInstance($data);
        } else {
            return $this->buildProxyInstance($data);
        }
    }

    /**
     * @return bool
     */
    public function hasMagicTrait()
    {
        $traits = class_uses($this->class);
        return isset($traits['Aura\Marshal\Entity\MagicArrayAccessTrait'])
            || isset($traits['Aura\Marshal\Entity\MagicPropertyTrait'])
            || is_subclass_of($this->class, 'Aura\Marshal\Entity\GenericEntity');
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    protected function buildMagicInstance(array $data)
    {
        $class = $this->class;

        $entity = new $class;

        foreach ($data as $field => $value) {
            $entity->$field = $value;
        }

        return $entity;
    }

    /**
     * @param array $data
     *
     * @return object
     */
    protected function buildProxyInstance(array $data){
        $entity = $this->createProxy();

        $writer = \Closure::bind(
            function ($object, $prop, $value) {
                $object->$prop = $value;
            },
            null,
            get_class($entity)
        );

        $unset = \Closure::bind(
            function ($object, $prop) {
                unset($object->$prop);
            },
            null,
            get_class($entity)
        );

        // set fields
        foreach ($data as $field => $value) {
            if ($value instanceof LazyInterface) {
                //unset() the property, so PHP is triggering __get call for lazy loaded properties
                $unset($entity, $field);
                //rename field, so __get use the correct temp property to resolve lazy loading
                $field = '_lazy_' . $field;
            }
            $writer($entity, $field, $value);
        }

        return $entity;
    }

    /**
     * Creates a proxy class, providing lazy loading functionality using __get magic method only at runtime.
     *
     * @return object
     */
    protected function createProxy()
    {
        $class = $this->class;

        $reflection = new \ReflectionClass($class);
        $shortName = $reflection->getShortName();
        $classProxy = $shortName . 'MarshalProxy';
        $fullProxyName = $classProxy;
        $namespace = $reflection->getNamespaceName();


        if ($namespace) {
            $fullProxyName = $namespace . '\\' . $classProxy;
        }

        if (class_exists($fullProxyName, false)) {
            return new $fullProxyName;
        }

        $code = <<<'EOF'
class %classProxy% extends %shortName% {
    public function __get($field)
    {
        $lazyField = '_lazy_' .$field;
        if (!isset($this->$lazyField)) {
            throw new \Exception(sprintf('Property %s not found', $field));
        }

        // get the property value
        $value = $this->$lazyField;

        // is it a Lazy placeholder?
        if ($value instanceof \Aura\Marshal\Lazy\LazyInterface) {
            // replace the Lazy placeholder with the real object
            $value = $value->get($this);
            $this->$field = $value;
            unset($this->$lazyField);
        }

        return $value;
    }
}
EOF;
        $code = str_replace(['%classProxy%', '%shortName%'], [$classProxy, $shortName], $code);

        if ($namespace) {
            $code = "namespace $namespace { $code }";
        }

        eval($code);

        return new $fullProxyName;
    }

}
