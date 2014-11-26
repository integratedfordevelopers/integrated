<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Solr\Type
{
    use Integrated\Bundle\ContentBundle\Solr\Type\ContentType;

    use Integrated\Common\Content\ContentInterface;
    use Integrated\Common\Converter\Container;
    use Integrated\Common\Converter\ContainerInterface;

    use stdClass;

    /**
     * @covers Integrated\Bundle\ContentBundle\Solr\Type\ContentType
     *
     * @author Jan Sanne Mulder <jansanne@e-active.nl>
     */
    class ContentTypeTest extends \PHPUnit_Framework_TestCase
    {
        public function testInterface()
        {
            self::assertInstanceOf('Integrated\\Common\\Converter\\Type\\TypeInterface', $this->getInstance());
        }

        /**
         * @dataProvider buildProvider
         */
        public function testBuild(ContentInterface $content, array $expected)
        {
            $container = $this->getContainer();

            $this->getInstance()->build($container, $content);
            $this->getInstance()->build($container, $content); // should clear previous build

            self::assertEquals($expected, $container->toArray());
        }

        public function buildProvider()
        {
            return [
                [
                    new Fixtures\Object1(),
                    ['id' => ['type1-id1'], 'type_name' => ['type1'], 'type_class' => ['Integrated\\Bundle\\ContentBundle\\Tests\\Solr\\Type\\Fixtures\\Object1'], 'type_id' => ['id1']]
                ],
                [
                    new Fixtures\Object2(),
                    ['id' => ['type2-id2'], 'type_name' => ['type2'], 'type_class' => ['Integrated\\Bundle\\ContentBundle\\Tests\\Solr\\Type\\Fixtures\\Object2'], 'type_id' => ['id2']]
                ],
                [
                    new Fixtures\__CG__\ProxyObject(),
                    ['id' => ['proxy-type-proxy-id'], 'type_name' => ['proxy-type'], 'type_class' => ['ProxyObject'], 'type_id' => ['proxy-id']]
                ]
            ];
        }

        public function testBuildNoContent()
        {
            $container = $this->getMock('Integrated\\Common\\Converter\\ContainerInterface');
            $container->expects($this->never())
                ->method($this->anything());

            /** @var ContainerInterface $container */

            $this->getInstance()->build($container, new stdClass());
        }

        public function testGetName()
        {
            self::assertEquals('integrated.content', $this->getInstance()->getName());
        }

        /**
         * @return ContentType
         */
        protected function getInstance()
        {
            return new ContentType();
        }

        /**
         * @return ContainerInterface
         */
        protected function getContainer()
        {
            // Easier to check end result when using an actual container instead of mocking it away. Also
            // the code coverage for the container class is ignored for these tests.

            return new Container();
        }
    }
}

namespace Integrated\Bundle\ContentBundle\Tests\Solr\Type\Fixtures
{
    use Integrated\Common\Content\ContentInterface;

    class Object1 implements ContentInterface
    {
        public function getId()                      { return 'id1'; }
        public function getContentType()             { return 'type1'; }
        public function setContentType($contentType) { }
    }

    class Object2 implements ContentInterface
    {
        public function getId()                      { return 'id2'; }
        public function getContentType()             { return 'type2'; }
        public function setContentType($contentType) { }
    }
}

namespace Integrated\Bundle\ContentBundle\Tests\Solr\Type\Fixtures\__CG__
{
    use Integrated\Common\Content\ContentInterface;

    class ProxyObject implements ContentInterface
    {
        public function getId()                      { return 'proxy-id'; }
        public function getContentType()             { return 'proxy-type'; }
        public function setContentType($contentType) { }
    }
}
