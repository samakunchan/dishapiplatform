<?php

declare(strict_types=1);

namespace App\Authorizations;

interface ResourceAccessCheckerInterface
{
    const MESSAGE_ERROR = "It's not your resource";

    public function canAccess(?int $id): void;
}
