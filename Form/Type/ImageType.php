<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
