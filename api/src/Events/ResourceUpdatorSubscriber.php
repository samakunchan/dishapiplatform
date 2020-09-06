<?php

declare(strict_types=1);

namespace App\Events;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Recipe;
use App\Entity\User;
use App\Exceptions\AuthentificationException;
use App\Services\ResourceUpdatorInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResourceUpdatorSubscriber implements EventSubscriberInterface
{
    private ResourceUpdatorInterface $resourceUpdator;
    private EntityManagerInterface $manager;

    public function __construct(ResourceUpdatorInterface $resourceUpdator, EntityManagerInterface $manager)
    {
        $this->resourceUpdator = $resourceUpdator;
        $this->manager = $manager;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                ['checkDuplicateEmail', 66],
                ['checkAuthorization', EventPriorities::PRE_VALIDATE],
            ],
        ];
    }

    public function checkDuplicateEmail(ViewEvent $event)
    {
        $object = $event->getControllerResult();
        if ($object instanceof User && $event->getRequest()->getMethod() === Request::METHOD_POST) {
            if (!!$this->manager->getRepository(User::class)->findOneBy(['email'=> $object->getEmail()])){
                throw new AuthentificationException(Response::HTTP_FORBIDDEN, 'This email already exist in database');
            }
        }
        return false;
    }

    public function checkAuthorization(ViewEvent $event): void
    {
        $object = $event->getControllerResult();

        if ($object instanceof User || $object instanceof Recipe) {
            $user = $object instanceof User ? $object : $object->getAuthor();

            $canProcess = $this->resourceUpdator->process(
                $event->getRequest()->getMethod(),
                $user
            );

            if ($canProcess) {
                $user->setUpdatedAt(new DateTimeImmutable());
            }
        }
    }
}
