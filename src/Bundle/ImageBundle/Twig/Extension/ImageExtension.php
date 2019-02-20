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
     * @var array
     */
    private $mimicFormats;

    /**
     * @var ImageHandling
     */
    private $imageMimicHandling;

    /**
     * @param ImageHandling      $imageHandling
     * @param ImageTwig          $imageTwig
     * @param WebFormatConverter $webFormatConverter
     * @param array              $mimicFormats
     * @param ImageHandling      $imageMimicHandling
     */
    public function __construct(ImageHandling $imageHandling, ImageTwig $imageTwig, WebFormatConverter $webFormatConverter, array $mimicFormats, ImageHandling $imageMimicHandling)
    {
        $this->imageHandling = $imageHandling;
        $this->webFormatConverter = $webFormatConverter;
        $this->imageTwig = $imageTwig;
        $this->mimicFormats = $mimicFormats;
        $this->imageMimicHandling = $imageMimicHandling;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('integrated_image', [$this, 'image'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('image_json', [$this, 'imageJson'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('web_image', [$this, 'webImage'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('image', [$this, 'image'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param $image
     *
     * @return \Gregwar\ImageBundle\ImageHandler
     */
    public function imageJson($image)
    {
        if ($json = json_decode($image)) {
            $storageModel = StorageModelFactory::json($json);

            // Returns the image in a webformat
            try {
                $image = $this->webFormatConverter->convert($storageModel)->getPathname();
            } catch (\Exception $e) {
                // Set the fallback image
                $image = false;
            }

            if (\in_array($storageModel->getMetadata()->getExtension(), $this->mimicFormats)) {
                return $this->imageMimicHandling->open($image);
            }
        }

        return $this->imageHandling->open($image);
    }

    /**
     * @param $image
     *
     * @return \Gregwar\ImageBundle\ImageHandler
     */
    public function webImage($image)
    {
        if ($image instanceof StorageInterface) {
            try {
                // Returns the image in a webformat
                return $this->imageHandling->open($this->webFormatConverter->convert($image)->getPathname());
            } catch (\Exception $e) {
                // Set the fallback image
                $image = false;
            }
        }

        return $this->imageTwig->webImage($image);
    }

    /**
     * @param $image
     *
     * @return \Gregwar\ImageBundle\ImageHandler
     */
    public function image($image)
    {
        if ($image instanceof StorageInterface) {
            $metadata = $image->getMetadata();

            try {
                $image = $this->webFormatConverter->convert($image)->getPathname();
            } catch (\Exception $e) {
                // Set the fallback image
                $image = false;
            }

            if (\in_array($metadata->getExtension(), $this->mimicFormats)) {
                return $this->imageMimicHandling->open($image);
            }
        } elseif (filter_var($image, FILTER_VALIDATE_URL)) {
            return $this->imageMimicHandling->open($image);
        }

        //detect json format
        if (strpos($image, '{') === 0) {
            return $this->imageJson($image);
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
