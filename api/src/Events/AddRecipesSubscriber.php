<?php
declare(strict_types=1);

namespace App\Events;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Recipe;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AddRecipesSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['addDefaultRecipe', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function addDefaultRecipe(ViewEvent $event): void
    {
        $recipe = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($recipe instanceof Recipe && Request::METHOD_POST === $method) {
            $recipe->setSlug($recipe->getTitle());
            $recipe->setCreatedAt(new DateTimeImmutable());
        } elseif ($recipe instanceof Recipe && (Request::METHOD_PUT === $method || Request::METHOD_PATCH === $method)) {
            $recipe->setSlug($recipe->getTitle());
            $recipe->setUpdatedAt(new DateTimeImmutable());
        }
    }
}
