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

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Form\Mapping\Annotations as Type;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document type Image
 *
 * @author Johnny Borg <johnny@e-active.nl>
 *
 * @ODM\Document
 * @Type\Document("Video")
 */
class Video extends File
{
    /**
     * @var StorageInterface
     * @Type\Field(type="integrated_file")
     * @Assert\File(mimeTypes="video/*")
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage")
     */
    protected $file;
}
