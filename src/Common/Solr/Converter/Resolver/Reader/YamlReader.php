<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Converter\Resolver\Reader;

use Integrated\Common\Solr\Converter\ConverterSpecification;
use Integrated\Common\Solr\Converter\ConverterSpecificationInterface;
use Integrated\Common\Solr\Converter\Resolver\FileResolverReaderInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class YamlReader implements FileResolverReaderInterface
{
    /**
     * @param SplFileInfo $file
     *
     * @return ConverterSpecificationInterface[]
     */
    public function read(SplFileInfo $file)
    {
        $input = $file->getContents();

        if (!$data = Yaml::parse($input)) {
            return [];
        }

        $specs = [];

        if (isset($data['convert'])) {
            foreach ($data['convert'] as $config) {
                $spec = new ConverterSpecification();

                $spec->classes = $this->getClasses($config);
                $spec->fields = $this->getFields($config);
                $spec->id = $this->getId($config);

                $specs[] = $spec;
            }
        }

        // @todo implement ignore

        return $specs;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getClasses(array $data)
    {
        if (!isset($data['class'])) {
            return [];
        }

        $classes = $data['class'];

        if (!is_array($classes)) {
            $classes = [$classes];
        }

        return $classes;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getFields(array $data)
    {
        if (!isset($data['fields'])) {
            return [];
        }

        return $data['fields']; // @todo do integer check
    }

    /**
     * @param array $data
     *
     * @return string | null
     */
    protected function getId(array $data)
    {
        if (!isset($data['id'])) {
            return null;
        }

        return (string) $data['id'];
    }
}
