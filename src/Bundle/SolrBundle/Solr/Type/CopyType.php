<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Solr\Type;

use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeInterface;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class CopyType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        $fields = [];

        // first group the options by field name

        foreach ($options as $to => $from) {
            if (\is_array($from)) {
                $to = $from[$index = isset($from['name']) ? 'name' : key($from)];
                unset($from[$index]);
            } else {
                $from = [$from]; // convert simple config to advance config
            }

            $fields[$to][] = $from;
        }

        foreach ($fields as $to => $from) {
            $this->remove($container, $to);

            foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($from)) as $field) {
                foreach ((array) $container->get($field) as $value) {
                    $this->append($container, $to, $value);
                }
            }
        }
    }

    /**
     * @param ContainerInterface $container
     * @param string             $field
     */
    protected function remove(ContainerInterface $container, $field)
    {
        $container->remove($field);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $field
     * @param string             $value
     */
    protected function append(ContainerInterface $container, $field, $value)
    {
        $container->add($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.copy';
    }
}
