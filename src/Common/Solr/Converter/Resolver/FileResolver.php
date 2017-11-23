<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Converter\Resolver;

use Integrated\Common\Solr\Converter\ConverterSpecificationInterface;
use Integrated\Common\Solr\Converter\ConverterSpecificationResolverInterface;
use Symfony\Component\Finder\Finder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FileResolver implements ConverterSpecificationResolverInterface
{
    /**
     * @var FileResolverReaderInterface
     */
    protected $reader;

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var ConverterSpecificationInterface[]
     */
    protected $specs = [];

    public function __construct(FileResolverReaderInterface $reader, Finder $finder)
    {
        $this->reader = $reader;
        $this->finder = clone $finder;
    }

    protected function load()
    {
        if ($this->loaded !== false) {
            return;
        }

        $this->loaded = true;

        // the files that a read first have a lower priority then the last one so
        // evey new spec is added to the beginning of the array

        foreach ($this->finder as $file) {
            foreach ($this->reader->read($file) as $spec) {
                array_unshift($this->specs, $spec);
            }
        }
    }

    /**
     * @param $class
     *
     * @return bool
     */
    public function hasSpecification($class)
    {
        $this->load();

        foreach ($this->specs as $spec) {
            if ($spec->hasClass($class)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $class
     *
     * @return ConverterSpecificationInterface
     */
    public function getSpecification($class)
    {
        $this->load();

        foreach ($this->specs as $spec) {
            if ($spec->hasClass($class)) {
                return $spec;
            }
        }

        return null;
    }
}
