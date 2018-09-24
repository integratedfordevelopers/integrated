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

use Integrated\Bundle\SolrBundle\Solr\Type\CopyAppendType;

/**
 * @covers \Integrated\Bundle\SolrBundle\Solr\Type\CopyAppendType
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class CopyAppendTypeTest extends CopyTypeTest
{
    /**
     * @return array
     */
    public function buildProvider()
    {
        $data = parent::buildProvider();

        // Expected results should be double that of the none append copy type so double
        // the expected values from the parent data provider but only for field 3 and 4.

        foreach ($data as &$arguments) {
            foreach ($arguments[1] as $name => &$value) {
                if (\in_array($name, ['field3', 'field4'])) {
                    $value = array_merge($value, $value);
                }
            }
        }

        return $data;
    }

    public function testGetName()
    {
        self::assertEquals('integrated.copy.append', $this->getInstance()->getName());
    }

    /**
     * @return CopyAppendType
     */
    protected function getInstance()
    {
        return new CopyAppendType();
    }
}
