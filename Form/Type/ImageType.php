<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ImageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_image';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'integrated_file';
    }
}