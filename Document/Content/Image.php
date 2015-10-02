<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Content;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Common\Form\Mapping\Annotations as Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document type Image
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 *
 * @ODM\Document
 * @ODM\HasLifecycleCallbacks
 *
 * @Type\Document("Image")
 */
class Image extends File
{
    /**
     * {@inheritdoc}
     * @Type\Field(type="integrated_image")
     * @ODM\Field
     * @Assert\Image()
     */
    protected $file;

    /**
     * Get the upload dir for displaying uploaded files in the view
     *
     * @return string
     */
    protected function getUploadDir()
    {
        return 'uploads/images';
    }
}