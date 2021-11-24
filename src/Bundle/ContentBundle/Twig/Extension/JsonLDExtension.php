<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Twig\Extension;

use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class JsonLDExtension extends AbstractExtension
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('json_ld', [$this, 'encode'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function encode($value)
    {
        $serialized = $this->serializer->serialize($value, 'json-ld');

        if ($serialized && $serialized !== 'null') {
            return $serialized;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_content_json_ld_extension';
    }
}
