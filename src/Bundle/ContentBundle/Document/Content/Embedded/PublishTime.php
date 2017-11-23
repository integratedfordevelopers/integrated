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

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PublishTime
{
    /**
     * @var string
     */
    const DATE_MAX = '9999-12-31 00:00:00'; // @todo find a better way (INTEGRATED-429)

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var \DateTime
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
     *
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
     *
     * @return $this
     */
    public function setEndDate(\DateTime $endDate = null)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @param \DateTime $date
     *
     * @return bool
     */
    public function isPublished(\DateTime $date = null)
    {
        if (null === $date) {
            $date = new \DateTime();
        }

        return $this->startDate <= $date && $this->endDate >= $date;
    }
}
