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

use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentTypeFieldCollection as ContentTypeFieldCollectionTransformer;

use Integrated\Common\ContentType\Mapping\MetadataInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeFieldCollectionType extends AbstractType
{
	/**
	 * @inheritdoc
	 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		/** @var MetadataInterface $metadata */
		$metadata = $options['metadata'];

		foreach ($metadata->getFields() as $field) {
			$builder->add($field->getName(), 'content_type_field', [
				'label' => $field->getOption('label'),
				'field' => $field,
			]);
        }

        $builder->addModelTransformer(new ContentTypeFieldCollectionTransformer());
    }

	/**
	 * @inheritdoc
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setRequired(['metadata']);
		$resolver->setAllowedTypes(['metadata' => 'Integrated\\Common\\ContentType\\Mapping\\MetadataInterface']);
	}

	/**
	 * @inheritdoc
	 */
    public function getName()
    {
        return 'integrated_content_type_fields';
    }
}