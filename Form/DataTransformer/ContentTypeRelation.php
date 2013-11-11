<?php
namespace Integrated\Bundle\ContentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Relation;
use Integrated\Common\ContentType\ContentTypeInterface;

class ContentTypeRelation implements DataTransformerInterface
{
    /**
     * @var ContentTypeInterface
     */
    private $contentType;

    /**
     * @param ContentTypeInterface $contentType
     */
    public function __construct(ContentTypeInterface $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @param mixed $relation
     * @return array|mixed
     */
    public function transform($relation)
    {
        if ($relation instanceof Relation) {
            return array(
                'enabled' => true,
                'required' => $relation->getRequired(),
                'multiple' => (int) $relation->getMultiple()
            );
        }

        return array();

    }

    /**
     * @param mixed $value
     * @return Relation|mixed|null
     */
    public function reverseTransform($value)
    {
        if (is_array($value)) {
            if (!empty($value['enabled'])) {
                $relation = new Relation();
                $relation->setContentType($this->contentType)
                    ->setRequired(!empty($value['required']))
                    ->setMultiple(!empty($value['multiple']));

                return $relation;
            }
        }

        return null;
    }
}