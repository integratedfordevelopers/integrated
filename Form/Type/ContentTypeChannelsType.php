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

use Doctrine\Common\Persistence\ObjectRepository;

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;

use Integrated\Bundle\ContentBundle\Form\DataTransformer\ChannelsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypeChannelsType extends AbstractType
{
	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('options', 'choice', [
			'choices' => [
				''         => 'Enable channel field',
				'hidden'   => 'Enable but hide channel field',
				'disabled' => 'Disable channel field'
			],
			'required' => false,
		]);

		$builder->add('defaults', 'content_type_channel_collection', ['label' => 'Channels', 'required' => false]);

		$builder->addViewTransformer(new ChannelsTransformer());
	}

	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return 'integrated_content_type_channels';
	}
} 