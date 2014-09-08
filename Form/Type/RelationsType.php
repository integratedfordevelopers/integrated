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

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Integrated\Bundle\ContentBundle\Form\DataTransformer\Relations as RelationsTransformer;

use Integrated\Common\ContentType\ContentTypeInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationsType extends AbstractType
{
	/**
	 * @var ManagerRegistry
	 */
	private $manager;

	/**
	 * @param ManagerRegistry $manager
	 */
	public function __construct(ManagerRegistry $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		/** @var ContentTypeInterface $type */
		$type = $options['content_type'];

		foreach ($type->getRelations() as $relation) {
			$url = [];

			foreach ($relation->getContentTypes() as $contentType) {
				$url[] = $contentType->getType();
			}

			$builder->add($relation->getId(), 'hidden', [
				'attr' => [
					'data-title'    => $relation->getName(),
					'data-relation' => $relation->getId(),
					'data-url'      => implode('&', $url),
					'data-multiple' => $relation->getMultiple()
				]
			]);
		}

		$builder->addModelTransformer(new RelationsTransformer($type->getRelations(), $this->manager->getManager()));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(['data_class' => null]);
		$resolver->setRequired(['content_type']);
		$resolver->setAllowedTypes(['content_type' => 'Integrated\\Common\\ContentType\\ContentTypeInterface']);
	}

	public function getName()
	{
		return 'integrated_content_relations';
	}
}