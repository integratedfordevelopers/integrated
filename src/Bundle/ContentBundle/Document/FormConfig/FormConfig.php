<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\FormConfig;

use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use Integrated\Common\FormConfig\FormConfigInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDBUnique(fields="id")
 */
class FormConfig implements FormConfigInterface
{
    /**
     * @var string
     *
     * @Slug(fields={"name"}, separator="_")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var FormConfigFieldInterface[]
     */
    private $fields;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return FormConfig
     */
    public function setId(string $id): FormConfig
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return FormConfig
     */
    public function setName(string $name): FormConfig
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     * @return FormConfig
     */
    public function setContentType(string $contentType): FormConfig
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return FormConfigFieldInterface[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param FormConfigFieldInterface[] $fields
     * @return FormConfig
     */
    public function setFields(array $fields): FormConfig
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return FormConfigFieldInterface|null
     */
    public function getField($name)
    {
        foreach ($this->getFields() as $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasField($name)
    {
        foreach ($this->getFields() as $field) {
            if ($field->getName() == $name) {
                return true;
            }
        }

        return false;
    }
}
