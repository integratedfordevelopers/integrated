<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\FormConfig\Field;

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\DocumentField;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigFieldProviderInterface;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;

class ContentProvider implements FormConfigFieldProviderInterface
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadata;

    /**
     * @param MetadataFactoryInterface $metadata
     */
    public function __construct(MetadataFactoryInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(ContentTypeInterface $type): array
    {
        $fields = [];

        foreach ($this->metadata->getMetadata($type->getClass())->getFields() as $field) {
            $fields[] = new DocumentField(
                $field->getName(),
                $field->getType(),
                $field->getOptions()
            );
        }

        return $fields;
    }
}
