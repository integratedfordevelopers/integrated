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
use Integrated\Common\ContentType\Mapping\Metadata;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentTypeField as Transformer;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeField extends AbstractType
{
    /**
     * @var Metadata\ContentTypeField
     */
    protected $contentTypeField;

    /**
     * @param Metadata\ContentTypeField $contentTypeField
     */
    public function __construct(Metadata\ContentTypeField $contentTypeField)
    {
        $this->contentTypeField = $contentTypeField;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'enabled',
            'checkbox',
            array(
                'required' => false,
                'label' => $this->contentTypeField->getLabel()
            )
        );

        $builder->add(
            'required',
            'checkbox',
            array(
                'required' => false
            )
        );

        $transformer = new Transformer($this->contentTypeField);
        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'content_type_field';
    }
}