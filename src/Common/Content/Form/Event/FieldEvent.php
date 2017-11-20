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
use Integrated\Common\Form\Mapping\AttributeEditorInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FieldEvent extends FormEvent
{
    /**
     * @var AttributeEditorInterface
     */
    private $field;

    /**
     * @var array
     */
    private $options;

    /**
     * @var bool
     */
    private $ignore = false;

    /**
     * @var mixed
     */
    private $data = null;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        ContentTypeInterface $type,
        MetadataInterface $metadata,
        AttributeEditorInterface $field,
        array $options
    ) {
        parent::__construct($type, $metadata);

        $this->field = $field;
        $this->options = $options;
    }

    /**
     * @return AttributeEditorInterface
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param boolean $ignore
     */
    public function setIgnore($ignore)
    {
        $this->ignore = (bool)$ignore;
    }

    /**
     * @return boolean
     */
    public function isIgnored()
    {
        return $this->ignore;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
