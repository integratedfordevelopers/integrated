<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\Content;

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\PublishTime;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation;
use Integrated\Bundle\ContentBundle\Document\Content\Taxonomy;
use Integrated\Common\Content\PublishTimeInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ReferenceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Taxonomy
     */
    private $taxonomy;

    protected function setUp()
    {
        $this->taxonomy = new Taxonomy();
    }

    /**
     * Test published ReferenceByRelationId.
     */
    public function testPublishedReferenceByRelationId()
    {
        $relation = new Relation();
        $relation->setRelationId('test');

        // test unpublished

        $reference1 = new Taxonomy();
        $reference1->setDisabled(true);

        $relation->addReference($reference1);

        $this->taxonomy->addRelation($relation);

        $this->assertCount(0, $this->taxonomy->getReferencesByRelationId($relation->getRelationId()));
        $this->assertFalse($this->taxonomy->getReferenceByRelationId($relation->getRelationId()));

        // test published

        $publishTime = new PublishTime();
        $publishTime->setStartDate(new \DateTime());
        $publishTime->setEndDate(new \DateTime(PublishTimeInterface::DATE_MAX));

        $reference2 = new Taxonomy();
        $reference2->setPublishTime($publishTime);

        $relation->addReference($reference2);

        $this->assertCount(1, $this->taxonomy->getReferencesByRelationId($relation->getRelationId()));
        $this->assertSame($reference2, $this->taxonomy->getReferenceByRelationId($relation->getRelationId()));
        $this->assertEquals([$reference2], $this->taxonomy->getReferencesByRelationId($relation->getRelationId())->getValues());

        // test published parameter

        $this->assertCount(2, $this->taxonomy->getReferencesByRelationId($relation->getRelationId(), false));
        $this->assertEquals([$reference1, $reference2], $this->taxonomy->getReferencesByRelationId($relation->getRelationId(), false)->getValues());
    }
}
