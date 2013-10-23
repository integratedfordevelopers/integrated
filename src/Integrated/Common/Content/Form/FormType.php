<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form;

use Integrated\Common\ContentType\ContentTypeInterface;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FormType implements FormTypeInterface
{
	protected $contentType;

	/**
	 * @param ContentTypeInterface $contentType
	 */
	public function __construct(ContentTypeInterface $contentType)
	{
		$this->contentType = $contentType;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		foreach ($this->contentType->getFields() as $field)
		{
			$builder->add(
				$builder->create($field->getName(), $field->getType(), array('label' => $field->getLabel()))
			);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function finishView(FormView $view, FormInterface $form, array $options)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => $this->contentType->getClass(),
			'empty_data' => function(FormInterface $form) {
				return $this->contentType->create();
			},
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return 'form';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->contentType->getClass() . '::' . $this->contentType->getType();
	}
}