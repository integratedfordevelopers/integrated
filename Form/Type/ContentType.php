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

use Integrated\Bundle\WorkflowBundle\Form\DataTransformer\DefinitionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Integrated\Common\ContentType\Mapping\Metadata;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentType extends AbstractType
{
    /**
     * @var Metadata\ContentType
     */
    protected $contentType;

    /**
     * @param Metadata\ContentType $contentType
     */
    public function __construct(Metadata\ContentType $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('class', 'hidden');
        $builder->add('name', 'text', ['label' => 'Name']);

        $builder->add('fields', new ContentTypeFieldCollection($this->contentType->getFields()));
//		$builder->add('fields', 'content_type_fields', ['fields' => $this->contentType->getFields()]);

		$builder->add('channels', 'content_type_channels', ['property_path' => 'options[channels]']);

		foreach ($this->contentType->getOptions() as $option) {
			$ype = $builder->create('options_' . $option->getName(), $option->getType(), ['label' => ucfirst($option->getName())] + $option->getOptions())
				->setPropertyPath('options[' . $option->getName() . ']');

			$builder->add($ype);
		}
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'content_type';
    }
}