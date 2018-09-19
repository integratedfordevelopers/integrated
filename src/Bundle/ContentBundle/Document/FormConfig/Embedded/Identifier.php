<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded;

use Integrated\Common\FormConfig\FormConfigIdentifierInterface;

class Identifier implements FormConfigIdentifierInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $key;

    /**
     * @param string $type
     * @param string $key
     */
    public function __construct(string $type, string $key)
    {
        $this->type = $type;
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'key' => $this->key,
        ];
    }
}
