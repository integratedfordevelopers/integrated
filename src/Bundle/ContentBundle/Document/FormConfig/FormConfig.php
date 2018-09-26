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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Identifier;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use Integrated\Common\FormConfig\FormConfigIdentifierInterface;
use Integrated\Common\FormConfig\FormConfigEditableInterface;

class FormConfig implements FormConfigEditableInterface
{
    /**
     * @var array
     */
    private $id;

    /**
     * @var Identifier
     */
    private $idInstance;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var FormConfigFieldInterface[] | Collection
     */
    private $fields;

    /**
     * @param Identifier $id
     */
    public function __construct(Identifier $id)
    {
        $this->id = $id->toArray();
        $this->idInstance = $id;

        $this->fields = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): FormConfigIdentifierInterface
    {
        if ($this->idInstance === null) {
            $this->idInstance = new Identifier($this->id['type'], $this->id['key']);
        }

        return $this->idInstance;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(): array
    {
        return $this->fields->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function setFields(iterable $fields): void
    {
        $this->fields = new ArrayCollection();

        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    /**
     * @param FormConfigFieldInterface $field
     */
    private function addField(FormConfigFieldInterface $field): void
    {
        $this->fields[] = $field;
    }
}
