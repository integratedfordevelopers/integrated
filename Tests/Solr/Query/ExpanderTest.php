<?php

namespace Integrated\Bundle\SolrBundle\Tests\Solr\Query;

use Integrated\Bundle\SolrBundle\Solr\Query\Expander;
use Integrated\Common\Solr\Query\Expander\ExpansionInterface;

use Solarium\Core\Query\AbstractQuery;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ExpanderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Expander
     */
    protected $expander;

    public function setup()
    {
        $this->expander = new Expander();
    }

    public function testExpand()
    {
        /** @var ExpansionInterface | \PHPUnit_Framework_MockObject_MockObject $expansion1 */
        $expansion1 = $this->getMock(ExpansionInterface::class);

        $expansion1
            ->expects($this->once())
            ->method('supportsClass')
            ->willReturn(true)
        ;

        $expansion1
            ->expects($this->once())
            ->method('expand')
        ;

        /** @var ExpansionInterface | \PHPUnit_Framework_MockObject_MockObject $expansion2 */
        $expansion2 = $this->getMock(ExpansionInterface::class);

        $expansion2
            ->expects($this->once())
            ->method('supportsClass')
            ->willReturn(false)
        ;

        $expansion2
            ->expects($this->never())
            ->method('expand')
        ;

        /** @var AbstractQuery |  $query */
        $query = $this->getMock(AbstractQuery::class);

        $this->assertSame($this->expander, $this->expander->addExpansion($expansion1));
        $this->assertSame($this->expander, $this->expander->addExpansion($expansion2));
        $this->assertSame($query, $this->expander->expand($query));
    }
}
