<?php
namespace Integrated\Bundle\ContentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Integrated\MongoDB\ContentType\Document\Embedded\Field;
use Integrated\Common\ContentType\Mapping\Metadata;

class ContentTypeField implements DataTransformerInterface
{
    /**
     * @var Metadata\ContentTypeField
     */
    private $contentTypeField;

    /**
     * @param Metadata\ContentTypeField $contentTypeField
     */
    public function __construct(Metadata\ContentTypeField $contentTypeField)
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
            return array(
                'enabled' => true,
                'required' => $field->getRequired()
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

                $field->setRequired();
                $field->setName($this->contentTypeField->getName())
                      ->setOptions($options);
                return $field;
            }
        }

        return null;
    }
}