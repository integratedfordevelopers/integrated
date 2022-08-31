<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Form\DataMapper;

use Symfony\Component\Form\Extension\Core\DataMapper\DataMapper;
use Symfony\Component\Form\FormInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UserMapper extends DataMapper
{
    /**
     * {@inheritdoc}
     */
    public function mapFormsToData($forms, &$data): void
    {
        // we only want properties to be mapped back when the form is enabled else nothing should
        // be written to the $data.

        $enabled = true;

        foreach ($forms as $form) {
            if ($form instanceof FormInterface && $form->getName() == 'enabled') {
                $enabled = $form->getData();
            }
        }

        if ($enabled) {
            parent::mapFormsToData($forms, $data);
        }
    }
}
