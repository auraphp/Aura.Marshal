<?php
/**
 * Loader
 */
$loader->add('Aura\Marshal\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Services
 */
$di->set('marshal_manager', $di->lazyNew('Aura\Marshal\Manager'));

/**
 * Aura\Marshal\Manager
 */
$di->params['Aura\Marshal\Manager'] = [
    'type_builder' => $di->lazyNew('Aura\Marshal\Type\Builder'),
    'relation_builder' => $di->lazyNew('Aura\Marshal\Relation\Builder'),
];
