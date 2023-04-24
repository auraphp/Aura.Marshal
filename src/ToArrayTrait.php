<?php

namespace Aura\Marshal;

use Aura\Marshal\Lazy\GenericLazy;

trait ToArrayTrait
{
    public function toArray(): array
    {
        return array_map(
            function ($entity)
            {
                if ($entity instanceof GenericLazy) {
                    $entity = $entity->get($this);
                }

                if ($entity instanceof ToArrayInterface) {
                    return $entity->toArray();
                }

                if (is_object($entity)) {
                    return (array) $entity;
                }

                return $entity;
            },
            $this->data
        );
    }
}
