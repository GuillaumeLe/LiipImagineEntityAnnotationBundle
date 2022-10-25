<?php

namespace Leroy\LiipImagineEntityAnnotationBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Leroy\LiipImagineEntityAnnotationBundle\Service\LiipFilterEntityService;

class LiipFilterHydrationSubscriber implements EventSubscriber
{
    private LiipFilterEntityService $liipFilterEntityService;

    public function __construct(LiipFilterEntityService $liipFilterEntityService)
    {
        $this->liipFilterEntityService = $liipFilterEntityService;
    }
    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array
    {
        return [Events::postLoad];
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $this->liipFilterEntityService->resolveFilters($entity);
    }
}
