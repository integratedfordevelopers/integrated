<?php

namespace Integrated\Bundle\StorageBundle\Form\Type;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ImageType extends FileType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'integrated_image';
    }
}
