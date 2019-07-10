<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImportBundle\Document\Embedded;

/**
 * Embedded document ImportField.
 */
class ImportField
{
    /**
     * @var int
     */
    protected $column;

    /**
     * @var string
     */
    protected $sourceField;

    /**
     * @var string
     */
    protected $mappedField;

    /**
     * @var array
     */
    protected $options = [];

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return int
     */
    public function getColumn(): int
    {
        return $this->column;
    }

    /**
     * @param int $column
     */
    public function setColumn(int $column): void
    {
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getSourceField(): ?string
    {
        return $this->sourceField;
    }

    /**
     * @param string $sourceField
     */
    public function setSourceField(string $sourceField): void
    {
        $this->sourceField = $sourceField;
    }

    /**
     * @return string
     */
    public function getMappedField(): ?string
    {
        return $this->mappedField;
    }

    /**
     * @param string $mappedField
     */
    public function setMappedField(string $mappedField): void
    {
        $this->mappedField = $mappedField;
    }

    /**
     * Set the options of the field.
     *
     * @param array $options The options of the form field
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }
}
