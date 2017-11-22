<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImageBundle\Converter;

use Doctrine\Common\Collections\ArrayCollection;

use Integrated\Bundle\ImageBundle\Converter\Helper\ExtensionHelper;
use Integrated\Bundle\ImageBundle\Exception\FormatException;

use Integrated\Bundle\StorageBundle\Storage\Cache\AppCache;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class Container
{
    /**
     * @var ArrayCollection|AdapterInterface[]
     */
    private $adapters;

    /**
     * Initialize class properties
     */
    public function __construct()
    {
        $this->adapters = new ArrayCollection();
    }

    /**
     * @param AdapterInterface $converter
     */
    public function add(AdapterInterface $converter)
    {
        $this->adapters->add($converter);
    }

    /**
     * @param string $outputFormat
     * @param StorageInterface $image
     * @return AdapterInterface
     * @throws FormatException
     */
    public function find($outputFormat, StorageInterface $image)
    {
        foreach ($this->adapters as $converter) {
            if (ExtensionHelper::caseTransformBoth($converter->formats())->contains($image->getMetadata()->getExtension())) {
                return $converter;
            }
        }

        throw FormatException::noSupportingConverter($image->getMetadata()->getExtension(), $outputFormat);
    }

    /**
     * @return ArrayCollection
     */
    public function formats()
    {
        $supported = new ArrayCollection();

        // Carefully playing with a big o notation, but i do not think there will be muchos adapteros
        foreach ($this->adapters as $adapter) {
            foreach ($adapter->formats() as $format) {
                if (!$supported->contains($format)) {
                    $supported->add($format);
                }
            }
        }

        return ExtensionHelper::caseTransformBoth($supported);
    }
}
