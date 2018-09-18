<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field;

use Integrated\Common\FormConfig\FormConfigFieldInterface;

class DocumentField implements FormConfigFieldInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $name
     * @param string $type
     * @param array  $options
     */
    public function __construct(string $name, string $type, array $options)
    {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
