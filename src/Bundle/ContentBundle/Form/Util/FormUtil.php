<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Util;

use Symfony\Component\Form\FormInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FormUtil
{
    /**
     * @param FormInterface $form
     *
     * @return FormInterface
     */
    public static function getRootForm(FormInterface $form)
    {
        $rootForm = $form->getParent();
        while ($rootForm->getParent()) {
            $rootForm = $rootForm->getParent();
        }

        return $rootForm;
    }
}
