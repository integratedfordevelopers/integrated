<?php
namespace Integrated\Bundle\ContentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ContentTypeFieldCollection implements DataTransformerInterface
{
    /**
     * @param mixed $field
     * @return array|mixed
     */
    public function transform($field)
    {
        return $field;
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