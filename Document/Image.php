<?php

namespace Integrated\Bundle\StorageBundle\Document;

use Integrated\Common\Form\Mapping\Annotations as Type;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * All data which might be required can be fetched from the upper class.
 * This empty class is for concrete implementations in the future.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 *
 * @ODM\Document
 * @Type\Document("File")
 */
class Image extends File
{

}
