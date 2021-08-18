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
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BuilderEvent extends FormEvent
{
    /**
     * @var FormBuilderInterface
     */
    private $builder;

    /**
     * @var array
     */
    private $options;

    /**
     * @var string|null
     */
    private $field = null;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        ContentTypeInterface $contentType,
        MetadataInterface $metadata,
        FormBuilderInterface $builder,
        array $options,
        $field = null
    ) {
        parent::__construct($contentType, $metadata);

        $this->builder = $builder;
        $this->options = $options;
        $this->field = $field !== null ? (string) $field : null;
    }

    /**
     * @return FormBuilderInterface
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function getField()
    {
        return $this->field;
    }
}
