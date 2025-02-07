<?php

namespace Aura\Marshal;

use Aura\Marshal\Entity\Builder;
use Aura\Marshal\MockEntity;

/**
 * @method MockEntity newInstance(array<int|string, mixed> $data)
 */
class MockEntityBuilder extends Builder
{
    protected $class = 'Aura\Marshal\MockEntity';
}
