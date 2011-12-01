<?php
namespace Aura\Marshal;
require_once dirname(__DIR__) . '/src.php';
return new Manager(new Type\Builder, new Relation\Builder);