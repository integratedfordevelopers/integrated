<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ProxyType extends AbstractType
{
	/**
	 * @var FormTypeInterface
	 */
	private $type;

	/**
	 * @var string
	 */
	private $name = null;

	/**
	 * @param FormTypeInterface $type
	 * @param string $name
	 */
	public function __construct(FormTypeInterface $type, $name = null)
	{
		$this->type = $type;
		$this->name = $name ? (string) $name : null;
	}

    /**
   	 * {@inheritdoc}
   	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$this->type->buildForm($builder, $options);
	}

    /**
   	 * {@inheritdoc}
   	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$this->type->buildView($view, $form, $options);
	}

    /**
   	 * {@inheritdoc}
   	 */
	public function finishView(FormView $view, FormInterface $form, array $options)
	{
		$this->type->finishView($view, $form, $options);

		if (!$this->type->getName()) {
			return;
		}

		$name = $this->getName() ? $this->name : $this->getParent();

		// add the original name to the block_prefixed after the proxy name

		$blocks = [];

		foreach ($view->vars['block_prefixes'] as $prefix) {
			$blocks[] = $prefix;

			if ($prefix == $name) {
				$blocks[] = $this->type->getName();
			}
		}

		$view->vars['block_prefixes'] = $blocks;
	}

	/**
	 * {@inheritdoc}
	 */
    public function configureOptions(OptionsResolver $resolver)
    {
        $this->type->setDefaultOptions($resolver);
    }

    /**
   	 * {@inheritdoc}
   	 */
	public function getParent()
	{
		return $this->type->getParent();
	}

    /**
   	 * {@inheritdoc}
   	 */
	public function getName()
	{
		return $this->name === null ? $this->type->getName() : $this->name;
	}
}
