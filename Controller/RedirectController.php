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

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\StorageBundle\Storage\Reflection\Document\DoctrineDocument;
use Integrated\Bundle\StorageBundle\Storage\Reflection\ReflectionCacheInterface;

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class RedirectController
{
    /**
     * @var ReflectionCacheInterface
     */
    private $reflection;

    /**
     * @param ReflectionCacheInterface $reflection
     */
    public function __construct(ReflectionCacheInterface $reflection)
    {
        $this->reflection = $reflection;
    }

    /**
     * @param Content $document
     * @return RedirectResponse
     */
    public function objectAction(Content $document)
    {
        // Read properties in the document containing a storage object
        foreach ($this->reflection->getPropertyReflectionClass(get_class($document))->getTargetProperties() as $property) {
            // Read out a property an check if its there is something and not void
            $reader = new DoctrineDocument($document);
            if ($storage = $reader->get($property->getPropertyName())) {
                // Sanity check
                if ($storage instanceof StorageInterface) {
                    // Send the request to the new place
                    return new RedirectResponse(
                        $storage->getPathname(),
                        Response::HTTP_MOVED_PERMANENTLY
                    );
                } else {
                    // This may never happen, reflection gave an invalid result
                    throw new \LogicException(
                        'Invalid instance %s provided trough reflection while %s was expected.',
                        is_object($storage) ? get_class($storage) : gettype($storage),
                        StorageInterface::class
                    );
                }
            }
        }

        // Everything ends here, no file found in the property
        throw new NotFoundHttpException(
            'There is file %s found in the object',
            $document->getId()
        );
    }
}
