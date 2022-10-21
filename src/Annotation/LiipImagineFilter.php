<?php

namespace Leroy\LiipImagineEntityAnnotationBundle\Annotation;


/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class LiipImagineFilter
{
    private ?string $filter = null;
    private ?string $path = null;
    private ?string $vichField = null;

    public function __construct(string $filter, string $path, string $vichField)
    {
        $this->filter = $filter;
        $this->path = $path;
        $this->vichField = $vichField;
    }

    public function getFilter(): ?string
    {
        return $this->filter;
    }

    public function setFilter(string $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }
}
