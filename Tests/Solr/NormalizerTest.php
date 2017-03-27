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
     * @dataProvider normalizeProvider
     */
    public function testNormalize($expected, $actual)
    {
        $this->assertEquals($expected, Normalizer::normalize($actual));
    }

    /**
     * @return array
     */
    public function normalizeProvider()
    {
        return [
            'strtolower' => [
                'test', 'Test'
            ],
            'trim' => [
                'test', '  test  '
            ],
            'diacritics' => [
                'eeaaooii', 'éëáäóöíï'
            ],
            'new lines and tabs' => [
                'test test', "\ntest\n\ttest\t"
            ],
            'reduce spaces' => [
                'test test', "test  test"
            ],
            'mixed' => [
                'test eaoi test', "\n Test\n\t éäóï \t\n TEST"
            ]
        ];
    }
}
