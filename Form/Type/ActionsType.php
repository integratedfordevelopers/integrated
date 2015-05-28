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

use Integrated\Common\Form\Type\ActionsType as BaseActionsType;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ActionsType extends BaseActionsType
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('integrated_content_actions', [
            'create'         => ['type' => 'submit', 'options' => ['label' => 'Create']],
            'save'           => ['type' => 'submit', 'options' => ['label' => 'Save']],
            'delete'         => ['type' => 'submit', 'options' => ['label' => 'Delete']],
            'back'           => ['type' => 'submit', 'options' => ['label' => 'Back', 'attr' => ['formnovalidate' => 'formnovalidate']]],
            'reload'         => ['type' => 'submit', 'options' => ['label' => 'Reload', 'attr' => ['formnovalidate' => 'formnovalidate']]],
            'reload_changed' => ['type' => 'submit', 'options' => ['label' => 'Reload (keep changes)', 'attr' => ['formnovalidate' => 'formnovalidate']]],
            'cancel'         => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'button_class' => 'default', 'attr' => ['formnovalidate' => 'formnovalidate', 'data-dismiss' => 'modal']]],
        ]);
    }
}
