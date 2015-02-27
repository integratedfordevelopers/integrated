<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Tests\Solr\Type;

use Integrated\Bundle\SolrBundle\Solr\Type\FieldAppendMapperType;

/**
 * @covers Integrated\Bundle\SolrBundle\Solr\Type\FieldAppendMapperType
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FieldAppendMapperTypeTest extends FieldMapperTypeTest
{
    public function buildProvider()
    {
        $data = parent::buildProvider();

        // Expected results should be double that of the none append field mapper so double
        // the expected values from the parent data provider.

        foreach ($data as &$arguments) {
            foreach ($arguments[1] as &$value) {
                $value = array_merge($value, $value);
            }
        }

        return $data;
    }

    public function testGetName()
    {
        self::assertEquals('integrated.fields.append', $this->getInstance()->getName());
    }

    /**
     * @return FieldAppendMapperType
     */
    protected function getInstance()
    {
        return new FieldAppendMapperType();
    }
}
