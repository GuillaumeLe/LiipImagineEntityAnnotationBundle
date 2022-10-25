<?php

namespace Leroy\LiipImagineEntityAnnotationBundle\Test\Service;

use Leroy\LiipImagineEntityAnnotationBundle\Annotation\LiipImagineFilter;
use Leroy\LiipImagineEntityAnnotationBundle\Service\LiipFilterEntityService;
use Liip\ImagineBundle\Service\FilterService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class EntityWithoutFilter
{
    public $id;
}

class EntityWithFilter
{
    public $path = "/avatar-emil.jpg";
    #[LiipImagineFilter(filter: 'background', path: 'path')]
    public string $background = '';
}

class LiipFilterEntityServiceTest extends TestCase
{
    private LiipFilterEntityService $liipFilterEntityService;

    // Mocks
    private MockObject $filterServiceMock;
    private MockObject $uploaderHelperMock;

    protected function setUp(): void
    {
        parent::setup();
        $this->filterServiceMock = $this->createMock(FilterService::class);
        $this->uploaderHelperMock = $this->createMock(UploaderHelper::class);
        $this->liipFilterEntityService = new LiipFilterEntityService(
            new PropertyAccessor(),
            $this->filterServiceMock,
            $this->uploaderHelperMock
        );
    }

    /**
     * @covers LiipFilterEntityService::resolveFilters
     */
    public function testResolveFiltersNotMappedEntity(): void
    {
        $entity = new EntityWithoutFilter();
        $this->liipFilterEntityService->resolveFilters($entity);
        $this->uploaderHelperMock->expects($this->never())->method('asset');
        $this->filterServiceMock->expects($this->never())->method('getUrlOfFilteredImage');
    }


    /**
     * @covers LiipFilterEntityService::resolveFilters
     */
    public function testResolveFiltersMappedEntity(): void
    {
        $entity = new EntityWithFilter();
        $this->uploaderHelperMock->expects($this->never())->method('asset');
        $this->filterServiceMock->method('getUrlOfFilteredImage')->willReturn('new_path');
        $this->liipFilterEntityService->resolveFilters($entity);
        $this->assertEquals('new_path', $entity->background);
    }
}
