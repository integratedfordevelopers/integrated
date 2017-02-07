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

use Gregwar\ImageBundle\Extensions\ImageTwig;
use Gregwar\ImageBundle\Services\ImageHandling;

use Integrated\Bundle\ImageBundle\Converter\WebFormatConverter;
use Integrated\Bundle\ImageBundle\Factory\StorageModelFactory;

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

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
     * @var WebFormatConverter
     */
    private $webFormatConverter;

    /**
     * @var ImageTwig
     */
    private $imageTwig;

    /**
     * @param ImageHandling $imageHandling
     * @param ImageTwig $imageTwig
     * @param WebFormatConverter $webFormatConverter
     */
    public function __construct(ImageHandling $imageHandling, ImageTwig $imageTwig, WebFormatConverter $webFormatConverter)
    {
        $this->imageHandling = $imageHandling;
        $this->webFormatConverter = $webFormatConverter;
        $this->imageTwig = $imageTwig;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('image_json', [$this, 'imageJson'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('web_image', [$this, 'webImage'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('image', [$this, 'image'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @param $image
     * @return \Gregwar\ImageBundle\ImageHandler
     */
    public function imageJson($image)
    {
        if ($json = json_decode($image)) {
            // Returns the image in a webformat
            $image = $this->webFormatConverter->convert(StorageModelFactory::json($json))->getPathname();
        }

        return $this->imageHandling->open((string) $image);
    }

    /**
     * @param $image
     * @return \Gregwar\ImageBundle\ImageHandler
     */
    public function webImage($image)
    {
        if ($image instanceof StorageInterface) {
            // Returns the image in a webformat
            $image = $this->webFormatConverter->convert($image)->getPathname();
        }

        return $this->imageTwig->webImage($image);
    }

    /**
     * @param $image
     * @return \Gregwar\ImageBundle\ImageHandler
     */
    public function image($image)
    {
        if ($image instanceof StorageInterface) {
            // Returns the image in a webformat
            $image = $this->webFormatConverter->convert($image)->getPathname();
        }

        return $this->imageHandling->open($image);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_image_json';
    }
}
