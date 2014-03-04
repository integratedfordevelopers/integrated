<?php
namespace Integrated\Bundle\ContentBundle\Form\DataTransformer;

use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;
use Symfony\Component\Form\DataTransformerInterface;

class ContentTypeFieldCollection implements DataTransformerInterface
{
    /**
     * @param mixed $fields
     * @return array $return
     */
    public function transform($fields)
    {
        $return = array();
        if (is_array($fields) || $fields instanceof \Traversable) {
            foreach ($fields as $field) {
                if ($field instanceof Field) {
                    $return[$field->getName()] = $field;
                }
            }
        }

        return $return;
    }

    /**
     * @param mixed $values
     * @return mixed|null
     */
    public function reverseTransform($values)
    {
        if (is_array($values) || $values instanceof \ArrayAccess) {
            foreach ($values as $key => $value) {
                if ($value === null) {
                    unset($values[$key]);
                }
            }
            return $values;
        }

        return null;
    }
}