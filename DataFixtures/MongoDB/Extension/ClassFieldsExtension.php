<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\DataFixtures\MongoDB\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait ClassFieldsExtension
{
    /**
     * @return ContainerInterface
     */
    abstract public function getContainer();

    /**
     * create a list of field based on the class.
     *
     * The field list will always reflect the current field configuration of the
     * class. It is possible to supply a list of fields that are required and that
     * should be ignored.
     *
     * @param string   $class
     * @param string[] $required  set the required flag for these fields
     * @param string[] $filter    a list of field that are white or black listed basted on $blacklist
     * @param bool     $blacklist if true then $filter is a blacklist and a white list if false
     *
     * @return Field[]
     */
    public function classfields($class, array $required = [], array $filter = [], $blacklist = true)
    {
        $fields = [];

        if (!$metadata = $this->getContainer()->get('integrated_content.metadata.factory')->getMetadata($class)) {
            return $fields;
        }

        $required = array_map('strtolower', $required);
        $filter = array_map('strtolower', $filter);
        $blacklist = (bool) $blacklist;

        if (!$blacklist) {
            $filter = array_merge($filter, $required);
        }

        foreach ($metadata->getFields() as $field) {
            if ($blacklist === in_array(strtolower($field->getName()), $filter)) {
                continue;
            }

            $fields[$field->getName()] = (new Field())
                ->setName($field->getName())
                ->setOptions($field->getOptions() + ['required' => in_array(strtolower($field->getName()), $required)]);
        }

        return $fields;
    }
}
