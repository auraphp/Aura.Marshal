<?php
namespace Aura\Marshal\_Config;

use Aura\Di\Config;
use Aura\Di\Container;

class Common extends Config
{
    public function define(Container $di)
    {
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
    }

    public function modify(Container $di)
    {
    }
}
