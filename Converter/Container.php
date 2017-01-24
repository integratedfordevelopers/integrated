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

use Integrated\Bundle\ImageBundle\Exception\FormatException;

use Integrated\Bundle\StorageBundle\Storage\Cache\AppCache;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class Container
{
    /**
     * @const string
     */
    const DIRECTORY = 'integrated/converter';

    /**
     * @var AppCache
     */
    private $cache;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var ArrayCollection|AdapterInterface[]
     */
    private $adapters;

    /**
     * @param string $directory
     * @param AppCache $cache
     */
    public function __construct($directory, AppCache $cache)
    {
        $this->cache = $cache;
        $this->directory = $directory;
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
        // Get a local file
        if (!($image instanceof \SplFileInfo)) {
            $image = $this->cache->path($image);
        }

        foreach ($this->adapters as $converter) {
            try {
                if ($converter->supports($outputFormat, $image)) {
                    return $converter;
                }
            } catch (FormatException $formatException) {
                // Intentionally left blank
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
                    // Dropzone does not seem to support lower/uppercase extension sensitivity
                    $supported->add(strtolower($format));
                    $supported->add(strtoupper($format));
                }
            }
        }

        return $supported;
    }
}
