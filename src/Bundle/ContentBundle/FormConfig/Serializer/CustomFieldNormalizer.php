<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\FormConfig\Serializer;

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\CustomField;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\FormConfigFieldTransformer;
use Integrated\Bundle\ContentBundle\Twig\Extension\FormConfigExtension;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CustomFieldNormalizer implements NormalizerInterface
{
    /**
     * @var FormConfigFieldTransformer
     */
    private $transformer;

    /**
     * @var FormConfigExtension
     */
    private $extension;

    /**
     * @param FormConfigFieldTransformer $transformer
     * @param FormConfigExtension        $extension
     */
    public function __construct(FormConfigFieldTransformer $transformer, FormConfigExtension $extension)
    {
        $this->transformer = $transformer;
        $this->extension = $extension;
    }

    /**
     * {@inheritdoc}
     *
     * @param CustomField $object  object to normalize
     * @param string      $format  format the normalization result will be encoded as
     * @param array       $context Context options for the normalizer
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$this->supportsNormalization($object)) {
            throw new InvalidArgumentException(sprintf('The object must be a instance of "%s".', CustomField::class));
        }

        return [
            'name' => $object->getName(),
            'type' => 'custom',
            'data' => [
                'type' => $this->extension->name($object),
                'label' => $this->extension->label($object),
                'form' => $this->transformer->transform($object),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof CustomField;
    }
}
