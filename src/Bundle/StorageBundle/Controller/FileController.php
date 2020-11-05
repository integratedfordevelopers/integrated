<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Controller;

use Gregwar\ImageBundle\Services\ImageHandling;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ImageBundle\Converter\WebFormatConverter;
use Integrated\Bundle\StorageBundle\Storage\Accessor\DoctrineDocument;
use Integrated\Bundle\StorageBundle\Storage\Mapping\MetadataFactoryInterface;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FileController
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadata;

    /**
     * @var WebFormatConverter
     */
    private $webFormatConverter;

    /**
     * @var ImageHandling
     */
    private $imageHandling;

    /**
     * @param MetadataFactoryInterface $metadata
     * @param WebFormatConverter       $webFormatConverter
     */
    public function __construct(MetadataFactoryInterface $metadata, WebFormatConverter $webFormatConverter, ImageHandling $imageHandling)
    {
        $this->metadata = $metadata;
        $this->webFormatConverter = $webFormatConverter;
        $this->imageHandling = $imageHandling;
    }

    /**
     * @param Content  $document
     * @param int|null $width
     * @param int|null $height
     *
     * @return RedirectResponse
     */
    public function fileAction(Content $document, int $width = null, int $height = null)
    {
        // Read properties in the document containing a storage object
        foreach ($this->metadata->getMetadata(\get_class($document))->getProperties() as $property) {
            // Read out a property an check if its there is something and not void
            $reader = new DoctrineDocument($document);
            if ($storage = $reader->get($property->getPropertyName())) {
                // Sanity check
                if ($storage instanceof StorageInterface) {
                    if ($width && $height) {
                        $file = $this->webFormatConverter->convert($storage)->getPathname();

                        return new RedirectResponse(
                            $this->imageHandling->open($file)->resize($width, $height, '#ffffff'),
                            Response::HTTP_MOVED_PERMANENTLY
                        );
                    }

                    // Send the request to the new place
                    return new RedirectResponse(
                        $storage->getPathname(),
                        Response::HTTP_MOVED_PERMANENTLY
                    );
                }
                // This may never happen, reflection gave an invalid result
                throw new \LogicException(
                    'Invalid instance %s provided trough reflection while %s was expected.',
                    \is_object($storage) ? \get_class($storage) : \gettype($storage),
                    StorageInterface::class
                );
            }
        }

        // Everything ends here, no file found in the property
        throw new NotFoundHttpException(
            sprintf('There is no file found in the %s object', $document->getId())
        );
    }
}
