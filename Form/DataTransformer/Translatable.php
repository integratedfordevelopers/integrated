<?php
namespace Integrated\Bundle\ContentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded as Embedded;
use Integrated\Common\ContentType\ContentTypeInterface;

class Translatable implements DataTransformerInterface
{
    /**
     * @param mixed $relation
     * @return array|mixed
     */
    public function transform($relation)
    {
        if ($relation instanceof Embedded\Translatable) {
            return $relation->getTranslations();
        }

        return array();

    }

    /**
     * @param mixed $value
     * @return Embedded\Translatable|null
     */
    public function reverseTransform($value)
    {
        if (is_array($value)) {

            $translatable = new Embedded\Translatable();
            $translatable->setTranslations($value);
            return $translatable;
        }

        return null;
    }
}