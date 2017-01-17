<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form\Event;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class OptionsEvent extends FormEvent
{
    /**
     * @var OptionsResolver
     */
    private $resolver;

    public function __construct(
        ContentTypeInterface $contentType,
        MetadataInterface $metadata,
        OptionsResolver $resolver
    ) {
        parent::__construct($contentType, $metadata);

        $this->resolver = $resolver;
    }

    /**
     * @return OptionsResolver
     */
    public function getResolver()
    {
        return $this->resolver;
    }
}
