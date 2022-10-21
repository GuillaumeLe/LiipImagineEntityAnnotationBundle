<?php

namespace Leroy\LiipImagineEntityAnnotationBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Leroy\LiipImagineEntityAnnotationBundle\Annotation\LiipImagineFilter;
use Leroy\LiipImagineEntityAnnotationBundle\Service\LiipFilterEntityService;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Service\FilterService;
use ReflectionObject;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class LiipFilterHydrationSubscriber implements EventSubscriber
{
    private CacheManager $imagineCacheManager;
    private FilterService $filterService;
    private PropertyAccessorInterface $propertyAccessor;
    private LiipFilterEntityService $liipFilterEntityService;

    // Temp 
    private UploaderHelper $uploaderHelper;


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
