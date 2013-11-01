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
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DeleteType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('delete', 'submit');
		$builder->add('back', 'submit');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'delete_type';
	}
} 