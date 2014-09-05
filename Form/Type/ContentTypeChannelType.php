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

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypeChannelType extends AbstractType
{
	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		/** @var Channel $channel */
		$channel = $options['channel'];

		$builder->add('selected', 'checkbox', [
			'required' => false,
			'label'    => $channel->getName()
		]);

		$builder->add('enforce', 'checkbox', [
			'required' => false,
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setRequired(['channel']);
		$resolver->setAllowedTypes(['channel' => 'Integrated\\Bundle\\ContentBundle\\Document\\Channel\\Channel']);
	}

	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return 'integrated_content_type_channel';
	}
}