<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Config\Provider;

use Integrated\Common\Converter\Config\TypeConfigInterface;
use Integrated\Common\Converter\Config\TypeProviderInterface;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
abstract class AbstractFileProvider implements TypeProviderInterface
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var TypeConfigInterface[][]
     */
    private $types = null;

    /**
     * @param Finder $finder
     * @param string $extension
     */
    protected function __construct(Finder $finder, $extension)
    {
        $finder = clone $finder;
        $finder->files()->name('*.' . $extension);

        $this->finder = $finder;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes($class)
    {
        $this->initialize();

        if (isset($this->types[$class])) {
            return $this->types[$class];
        }

        return [];
    }

    /**
     * Load the types if not already loaded
     */
    protected function initialize()
    {
        if ($this->types === null) {
            $this->types = [];

            foreach ($this->finder as $file) {
                $this->types = array_merge_recursive($this->types, $this->load($file));
            }
        }
    }

    /**
     * Load the types from the given file
     *
     * @param SplFileInfo $file
     * @return TypeConfigInterface[]
     */
    abstract protected function load(SplFileInfo $file);
}
