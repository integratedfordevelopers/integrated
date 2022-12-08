<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Mapping;

interface PropertyMetadataInterface
{
    public function getName(): string;

    /**
     * @return string[]
     */
    public function getFields(): array;

    public function getSeparator(): string;

    public function getLengthLimit(): int;

    public function getValue(object $object);

    /**
     * @param mixed $value
     */
    public function setValue(object $object, $value): void;
}
