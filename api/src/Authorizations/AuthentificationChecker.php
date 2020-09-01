<?php

declare(strict_types=1);

namespace App\Authorizations;

use App\Exceptions\ResourceAccessException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthentificationChecker implements AuthentificationCheckerInterface
{
    private ?UserInterface $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    public function isAuthenticated(): void
    {
        if (null === $this->user) {
            throw new ResourceAccessException(Response::HTTP_UNAUTHORIZED, self::ERROR_MESSAGE);
        }
    }
}
