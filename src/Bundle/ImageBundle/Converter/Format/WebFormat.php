<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImageBundle\Converter\Format;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ImageBundle\Converter\Helper\ExtensionHelper;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class WebFormat
{
    /**
     * @var ArrayCollection
     */
    private $webFormat;

    /**
     * @param array $webFormat
     */
    public function __construct(array $webFormat)
    {
        $this->webFormat = ExtensionHelper::caseTransformBoth(new ArrayCollection($webFormat));
    }

    /**
     * @param StorageInterface $storage
     *
     * @return bool
     */
    public function isWebFormat(StorageInterface $storage)
    {
        return $this->webFormat->contains($storage->getMetadata()->getExtension());
    }

    /**
     * @return ArrayCollection
     */
    public function getWebFormats()
    {
        return $this->webFormat;
    }
}
