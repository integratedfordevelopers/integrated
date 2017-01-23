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
use Integrated\Bundle\ImageBundle\Converter\Format\WebFormat;
use Integrated\Bundle\StorageBundle\Storage\Cache\AppCache;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class WebFormatConverter
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var WebFormat
     */
    private $webFormat;

    /**
     * @var AppCache
     */
    private $appCache;

    /**
     * @var string
     */
    private $format;

    /**
     * @param Container $container
     * @param WebFormat $webFormat
     * @param AppCache $appCache
     * @param string $format
     */
    public function __construct(Container $container, WebFormat $webFormat, AppCache $appCache, $format)
    {
        $this->container = $container;
        $this->webFormat = $webFormat;
        $this->appCache = $appCache;
        $this->format = $format;
    }

    /**
     * @param StorageInterface $image
     * @return \SplFileInfo
     */
    public function convert(StorageInterface $image)
    {
        if ($this->webFormat->isWebFormat($image)) {
            return $this->appCache->path($image);
        }

        return $this->container->find($this->format, $image)->convert($this->format, $this->appCache->path($image));
    }
}
