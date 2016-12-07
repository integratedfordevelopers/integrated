<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Solr;

use Integrated\Bundle\ContentBundle\Solr\Normalizer;

/**
 * @author Michael Jongman <michael@e-active.nl>
 */
class NormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *  Set up
     */
    protected function setUp()
    {
        $this->normalizer = new Normalizer();
    }

    /**
     * @dataProvider additionProvider
     */
    public function testNormalizerInput($expected, $actual, $message)
    {
        $this->assertEquals($expected, $this->normalizer->normalize($actual), $message);
    }

    /**
     * @return array
     */
    public function additionProvider()
    {
        return [
            [
                'test', 'Test', 'Testing strtolower'
            ],
            [
                'test', ' Test ', 'Testing trim'
            ],
            [
                'eaoi', 'éáóí', 'Filter umlauts'
            ],
            [
                'p test', "p\n Test\t", 'Filter new lines and tabs'
            ]
        ];
    }
}
