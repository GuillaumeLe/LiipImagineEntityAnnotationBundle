<?php

namespace Leroy\LiipImagineEntityAnnotationBundle\Service;

use Leroy\LiipImagineEntityAnnotationBundle\Annotation\LiipImagineFilter;
use Liip\ImagineBundle\Service\FilterService;
use ReflectionObject;
use ReflectionProperty;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class LiipFilterEntityService
{
    private FilterService $filterService;
    private PropertyAccessorInterface $propertyAccessor;

    private UploaderHelper $uploaderHelper;


    public function __construct(PropertyAccessorInterface $propertyAccessor, FilterService $filterService, UploaderHelper $uploaderHelper)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->filterService = $filterService;
        $this->uploaderHelper = $uploaderHelper;
    }

    public function resolveFilters(object &$entity): void
    {
        $reflection = new ReflectionObject($entity);
        foreach ($reflection->getProperties() as $property) {
            $this->resolvePropertyPath($entity, $property);
        }
    }

    private function resolvePropertyPath(object &$entity, ReflectionProperty $property)
    {
        $attributes = $property->getAttributes(LiipImagineFilter::class);
        if (count($attributes)) {
            if (count($attributes) > 1) {
                throw new \Exception("InvalidMappingException");
            }
            $args = current($attributes)->getArguments();
            $filter = $args["filter"];
            $vichField = isset($args["vichField"]) ? $args["vichField"] : null;
            $pathProperty = isset($args["path"]) ? $args["path"] : null;

            if ($vichField) {
                $path = $this->uploaderHelper->asset($entity, $vichField);
            } else if ($pathProperty) {
                $path = $this->propertyAccessor->getValue($entity, $pathProperty);
            } else {
                $path = null;
            }

            if ($path) {
                $filteredImage = $this->filterService->getUrlOfFilteredImage($path, $filter);

                $propertyName = $property->getName();
                $this->propertyAccessor->setValue($entity, $propertyName, $filteredImage);
            }
        }
    }
}
