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

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\CustomField;
use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\DocumentField;
use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\RelationField;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class FormConfigFieldTransformer implements DataTransformerInterface
{
    /**
     * @var FormConfigFieldInterface[]
     */
    private $fields;

    /**
     * @param FormConfigFieldInterface[] $fields
     */
    public function __construct(array $fields = [])
    {
        $this->fields = $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($field)
    {
        if ($field === null) {
            return null;
        }

        if (!$field instanceof DocumentField && !$field instanceof RelationField && !$field instanceof CustomField) {
            throw new UnexpectedTypeException($field, sprintf(
                '%s, %s or %s',
                DocumentField::class,
                RelationField::class,
                CustomField::class
            ));
        }

        if ($field instanceof DocumentField) {
            return [
                'id' => $field->getName(),
                'type' => 'document',
                'required' => $field->getOptions()['required'] ?? false,
            ];
        }

        if ($field instanceof RelationField) {
            return [
                'id' => $field->getName(),
                'type' => 'relation',
                'required' => $field->getOptions()['required'] ?? false,
            ];
        }

        $options = $field->getOptions();

        return [
            'id' => $field->getName(),
            'type' => 'custom',
            'required' => $options['required'] ?? false,
            'options' => [
                'label' => $options['label'] ?? '',
                'type' => $field->getType(),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if ($value === null) {
            return null;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        switch ($value['type']) {
            case 'document':
                $field = $this->getField($value['id'], DocumentField::class);

                if (!$field) {
                    throw new TransformationFailedException(sprintf(
                        'No document field with the name %s could be found.',
                        $value['id']
                    ));
                }

                $options = $field->getOptions();
                $options['required'] = $value['required'] ?? false;

                return new DocumentField($field->getName(), $field->getType(), $options);

            case 'relation':
                $field = $this->getField($value['id'], RelationField::class);

                if (!$field) {
                    throw new TransformationFailedException(sprintf(
                        'No relation field with the name %s could be found.',
                        $value['id']
                    ));
                }

                $options = $field->getOptions();
                $options['required'] = $value['required'] ?? false;

                return new RelationField($field->getRelation(), $options);

            case 'custom':
                return new CustomField(
                    $value['id'],
                    $value['options']['type'],
                    [
                        'label' => $value['options']['label'],
                        'required' => $value['required'],
                    ]
                );
        }

        throw new TransformationFailedException(sprintf(
            'The type %s is not supported, valid types are "document", "relation" and "custom".',
            $value['type']
        ));
    }

    /**
     * @param string $name
     * @param string $class
     *
     * @return DocumentField | RelationField
     */
    private function getField(string $name, string $class): ? FormConfigFieldInterface
    {
        foreach ($this->fields as $field) {
            if ($field instanceof $class && $field->getName() === $name) {
                return $field;
            }
        }

        return null;
    }
}
