<?php

namespace Aura\Marshal;

interface ToArrayInterface
{
    /**
     * @return mixed[]|array<int|string, mixed>
     */
    public function toArray(): array;
}
