<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Security\TwoFactor;

class Config
{
    /**
     * @var bool
     */
    private $required;

    /**
     * @var string[]
     */
    private $path;

    public function __construct(bool $required, string $formPath, string $checkPath, string $targetPath)
    {
        $this->required = $required;
        $this->path = [
            'form' => $formPath,
            'check' => $checkPath,
            'target' => $targetPath,
        ];
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getFormPath(): string
    {
        return $this->path['form'];
    }

    public function getCheckPath(): string
    {
        return $this->path['check'];
    }

    public function getTargetPath(): string
    {
        return $this->path['target'];
    }
}
