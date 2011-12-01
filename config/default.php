<?php
/**
 * Package prefix for autoloader.
 */
$loader->addPrefix('Aura\Marshal\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

$di->set('marshal_manager', function() use ($di) {
    return $di->newInstance('Aura\Marhal\Manager', array(
        'type_builder'     => $di->newInstance('Aura\Marshal\Type\Builder'),
        'relation_builder' => $di->newInstance('Aura\Marshal\Relation\Builder'),
    ));
});
