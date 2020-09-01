<?php

declare(strict_types=1);

namespace App\Authorizations;

interface AuthentificationCheckerInterface
{
    const ERROR_MESSAGE = 'You are not authenticated';

    public function isAuthenticated(): void;
}
