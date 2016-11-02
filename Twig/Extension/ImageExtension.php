<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImageBundle\Twig\Extension;

use Gregwar\Image\Image;
use Gregwar\ImageBundle\Services\ImageHandling;
use Integrated\Bundle\ImageBundle\Factory\StorageModelFactory;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ImageExtension extends \Twig_Extension
{
    /**
     * @var ImageHandling
     */
    private $imageHandling;

    /**
     * @param ImageHandling $imageHandling
     */
    public function __construct(ImageHandling $imageHandling)
    {
        $this->imageHandling = $imageHandling;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('image_json', [$this, 'imageJson'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param $image
     * @return \Gregwar\ImageBundle\ImageHandler
     */
    public function imageJson($image)
    {
        if ($json = json_decode($image)) {
            return $this->imageHandling->open(StorageModelFactory::json($json));
        }

        throw new \InvalidArgumentException('Argument is not a json string.');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'image_json';
    }
}
