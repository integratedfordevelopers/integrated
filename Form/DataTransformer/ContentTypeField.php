<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Integrated\Common\Form\Mapping\Metadata;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeField implements DataTransformerInterface
{
    /**
     * @var Metadata\ContentTypeAttribute
     */
    private $contentTypeField;

    /**
     * @param Metadata\ContentTypeAttribute $contentTypeField
     */
    public function __construct(Metadata\ContentTypeAttribute $contentTypeField)
    {
        $this->contentTypeField = $contentTypeField;
    }

    /**
     * @param mixed $field
     * @return array|mixed
     */
    public function transform($field)
    {
        if ($field instanceof Field) {

            $options = $field->getOptions();

            return array(
                'enabled' => true,
                'required' => !empty($options['required'])
            );
        }

        return array();
    }

    /**
     * @param mixed $value
     * @return Field|mixed|null
     */
    public function reverseTransform($value)
    {
        if (is_array($value)) {
            if (!empty($value['enabled'])) {
                $field = new Field();

                $options = $this->contentTypeField->getOptions();
                $options['required'] = !empty($value['required']);

                $field->setName($this->contentTypeField->getName())
                      ->setType($this->contentTypeField->getType())
                      ->setOptions($options);

                return $field;
            }
        }

        return null;
    }
}