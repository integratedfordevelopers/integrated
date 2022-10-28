<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Mapping\Metadata;

use Integrated\Bundle\SlugBundle\Mapping\PropertyMetadataInterface;
use ReflectionProperty;

class PropertyMetadata implements PropertyMetadataInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $fields = [];

    /**
     * @var string
     */
    private $separator;

    /**
     * @var int
     */
    private $lengthLimit;

    /**
     * @var ReflectionProperty
     */
    private $reflection;

    /**
     * @throws \ReflectionException
     */
    public function __construct(string $class, string $name, array $fields, string $separator, int $lengthLimit)
    {
        $this->name = $name;
        $this->fields = $fields;
        $this->separator = $separator;
        $this->lengthLimit = $lengthLimit;

        $this->reflection = new ReflectionProperty($class, $name);
        $this->reflection->setAccessible(true);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * @return int
     */
    public function getLengthLimit(): int
    {
        return $this->lengthLimit;
    }

    public function getValue(object $object)
    {
        return $this->reflection->getValue($object);
    }

    public function setValue(object $object, $value): void
    {
        $this->reflection->setValue($object, $value);
    }
}
