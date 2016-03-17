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
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class ChannelDomainType extends AbstractType
{
    public function getParent()
    {
        return 'text';
    }

    /**
	 * {@inheritDoc}
	 */
	public function getName()
	{
		return 'channel_domain';
	}
} 