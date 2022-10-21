# LiipImagine Entity Annotation Bundle

LiipImagineEntityAnnotationBundle has been design the make easier the usage of [LiipImagineBundle](https://github.com/liip/LiipImagineBundle) filter with Entity classes.

It's compatible with:
- [VichUploaderBundle](https://github.com/dustin10/VichUploaderBundle)

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require leroy/liip-imagine-entity-annotation-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require leroy/liip-imagine-entity-annotation-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Leroy\LiipImagineEntityAnnotationBundle\LiipImagineEntityAnnotationBundle::class => ['all' => true],
];
```

Usage
=====
Given the following filter config:
```yaml
liip_imagine:
    # valid drivers options include "gd" or "gmagick" or "imagick"
    driver: "gd"
    resolvers :
        default :
            web_path : ~
    filter_sets :
        cache : ~

        # the name of the "filter set"
        my_thumb :

            # adjust the image quality to 75%
            quality : 75

            # list of transformations to apply (the "filters")
            filters :

                # create a thumbnail: set size to 120x90 and use the "outbound" mode
                # to crop the image when the size ratio of the input differs
                thumbnail  : { size : [120, 90], mode : outbound }

                # create a 2px black border: center the thumbnail on a black background
                # 4px larger to create a 2px border around the final image
                background : { size : [124, 94], position : center, color : '#000000' }
```

### Basic (the original image path is store in a property)

If the original image path is directly accessible in a property of your entity:
```php

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Leroy\LiipImagineEntityAnnotationBundle\Annotation\LiipImagineFilter;

class ImageEntity
{
    public $path = "/my_image.jpg";

    // LiipImagine properties
    #[LiipImagineFilter(filter: 'my_thumb', path: 'path')]
    private string $thumbnail = '';

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }
}

```

### With VichUploader

If the original image is stored using VichUploaderBundle
```php
<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Leroy\LiipImagineEntityAnnotationBundle\Annotation\LiipImagineFilter;

#[ORM\Entity()]
/**
 * @Vich\Uploadable
 */
class ImageEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "datetime")]
    private \DateTime $updatedAt;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    /**
     * @Vich\UploadableField(mapping="images", fileNameProperty="filename")
     * @var File
     */
    private ?File $file;

    // LiipImagine properties
    #[LiipImagineFilter(filter: 'my_thumb', vichField: 'file')]

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\Datetime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(File $file = null): self
    {
        $this->file = $file;

        if ($file) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }
}

```