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
 * Class RecaptchaType
 * @package Integrated\Bundle\ContentBundle\Form\Type
 * @author Michael Jongman <michael@e-active.nl>
 */
class RecaptchaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_recaptcha';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'vihuvac_recaptcha';
    }
}
