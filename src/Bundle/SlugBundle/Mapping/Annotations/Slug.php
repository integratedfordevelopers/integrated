<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Mapping\Annotations;

use BadMethodCallException;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Slug
{
    /**
     * @var string[]
     */
    public $fields = [];

    /**
     * @var string
     */
    public $separator = '-';

    /**
     * @var int
     */
    public $lengthLimit = 200;

    /**
     * @throws BadMethodCallException
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set'.str_replace('_', '', $key);
            if (!method_exists($this, $method)) {
                throw new BadMethodCallException(sprintf("Unknown property '%s' on annotation '%s'.", $key, static::class));
            }
            $this->$method($value);
        }
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param string[] $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     */
    public function setSeparator(string $separator): void
    {
        $this->separator = $separator;
    }

    /**
     * @return int
     */
    public function getLengthLimit(): int
    {
        return $this->lengthLimit;
    }

    /**
     * @param int $lengthLimit
     */
    public function setLengthLimit(int $lengthLimit): void
    {
        $this->lengthLimit = $lengthLimit;
    }
}
