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

use Integrated\Bundle\ContentBundle\Form\DataTransformer\Relations as DataTransformer;
use Integrated\Common\ContentType\ContentTypeRelationInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Relations extends AbstractType
{
	/**
	 * @var ManagerRegistry
	 */
	private $mr;

	/**
	 * @param ManagerRegistry $mr
	 */
	public function __construct(ManagerRegistry $mr)
	{
		$this->mr = $mr;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		/** @var ContentTypeRelationInterface[] $relations */
		$relations = $options['relations'];

		foreach ($relations as $relation) {
			$url = [];

			foreach ($relation->getContentTypes() as $contentType) {
				$url[] = $contentType->getType();
			}

			$builder->add(
				$relation->getId(),
				'hidden',
				[
					'attr' => [
						'data-title'    => $relation->getName(),
						'data-relation' => $relation->getId(),
						'data-url'      => implode('&', $url),
						'data-multiple' => $relation->getMultiple()
					]
				]
			);
		}

//        $builder->addEventListener(
//            FormEvents::PRE_SET_DATA,
//            function (FormEvent $event) {
//                $form = $event->getForm();
//                $data = $event->getData();
//
//                var_dump($data);
//            }
//        );

		$transformer = new DataTransformer($relations, $this->mr->getManager());
		$builder->addModelTransformer($transformer);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(['data_class' => null]);
		$resolver->setRequired(['relations']);
	}

	public function getName()
	{
		return 'integrated_relations';
	}
}