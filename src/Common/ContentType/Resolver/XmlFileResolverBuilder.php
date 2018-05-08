<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Resolver;

use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Util\XmlUtils;

class XmlFileResolverBuilder extends MemoryResolverBuilder
{
    /**
     * @var FileLocatorInterface
     */
    private $locator;

    /**
     * @param FileLocatorInterface $locator
     */
    public function __construct(FileLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param string $resource
     */
    public function registerResource($resource)
    {
        $path = $this->locator->locate($resource);
        $xpath = new \DOMXPath(XmlUtils::loadFile($path));

        /** @var \DOMElement $element */
        foreach ($xpath->query('//content-types/content-type') as $element) {
            $contentType = new ContentType();

            $contentType->setLocked();

            $contentType->setId($element->getAttribute('id'));
            $contentType->setClass($element->getAttribute('class'));

            foreach ($element->getElementsByTagName('name') as $child) {
                $contentType->setName($child->nodeValue);
            }

            $this->addContentType($contentType);
        }
    }
}
