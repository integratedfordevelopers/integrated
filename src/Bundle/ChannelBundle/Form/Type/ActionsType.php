<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Form\Type;

use Integrated\Common\Form\Type\ActionsType as BaseActionsType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
        parent::__construct([
            'create' => [
                'type' => SubmitType::class,
                'options' => ['label' => 'Create configuration', 'translation_domain' => 'IntegratedChannelBundle'],
            ],
            'save' => [
                'type' => SubmitType::class,
                'options' => ['label' => 'Save', 'translation_domain' => 'IntegratedChannelBundle'],
            ],
            'delete' => [
                'type' => SubmitType::class,
                'options' => ['label' => 'Delete', 'translation_domain' => 'IntegratedChannelBundle'],
            ],
            'cancel' => [
                'type' => SubmitType::class,
                'options' => [
                    'label' => 'Cancel',
                    'translation_domain' => 'IntegratedChannelBundle',
                    'button_class' => 'default',
                ],
            ],
        ]);
    }
}
