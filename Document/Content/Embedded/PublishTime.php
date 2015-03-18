<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Content\Embedded;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\EmbeddedDocument
 */
class PublishTime
{
    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $startDate;

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $endDate;

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     * @return $this
     */
    public function setStartDate(\DateTime $startDate = null)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     * @return $this
     */
    public function setEndDate(\DateTime $endDate = null)
    {
        $this->endDate = $endDate;
        return $this;
    }
}
