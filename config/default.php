<?php
/**
 * Package prefix for autoloader.
 */
$loader->add('Aura\Marshal\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Instance params and setter values.
 */
$di->params['Aura\Marshal\Manager']['type_builder'] = $di->lazyNew('Aura\Marshal\Type\Builder');
$di->params['Aura\Marshal\Manager']['relation_builder'] = $di->lazyNew('Aura\Marshal\Relation\Builder');

/**
 * Dependency services.
 */
$di->set('marshal_manager', $di->lazyNew('Aura\Marshal\Manager'));
