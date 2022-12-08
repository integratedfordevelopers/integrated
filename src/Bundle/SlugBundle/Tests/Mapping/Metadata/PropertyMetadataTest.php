<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Tests\Mapping\Metadata;

use Integrated\Bundle\ContentBundle\Document\Content;
use Integrated\Bundle\SlugBundle\Mapping\Metadata\PropertyMetadata;
use PHPUnit\Framework\TestCase;

class PropertyMetadataTest extends TestCase
{
    /**
     * @dataProvider propertyProvider
     */
    public function testProperties(string $class, string $name, array $fields, string $separator, int $lengthLimit, string $value)
    {
        $metadata = new PropertyMetadata($class, $name, $fields, $separator, $lengthLimit);

        $this->assertSame($name, $metadata->getName());
        $this->assertSame($fields, $metadata->getFields());
        $this->assertSame($separator, $metadata->getSeparator());
        $this->assertSame($lengthLimit, $metadata->getLengthLimit());

        $object = $this->getMockBuilder($class)->getMock();

        $metadata->setValue($object, $value);
        $this->assertSame($metadata->getValue($object), $value);
    }

    public function propertyProvider(): array
    {
        return [
            [
                Content\Article::class,
                'slug',
                ['id', 'title'],
                '-',
                200,
                'article/slug-value',
            ],
            [
                Content\News::class,
                'slug',
                ['title'],
                '_',
                100,
                'news/slug-value',
            ],
            [
                Content\Relation\Company::class,
                'slug',
                ['name'],
                '-',
                200,
                'company/slug-value',
            ],
        ];
    }
}
