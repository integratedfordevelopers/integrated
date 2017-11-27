<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Content;

use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @Type\Document("Event")
 */
class Event extends Article
{
    /**
     * @var \DateTime
     * @Type\Field(type="Integrated\Bundle\FormTypeBundle\Form\Type\DateTimeType")
     */
    protected $startDate;

    /**
     * @var \DateTime
     * @Type\Field(type="Integrated\Bundle\FormTypeBundle\Form\Type\DateTimeType")
     */
    protected $endDate;

    /**
     * @var string
     * @Type\Field
     */
    protected $website;

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
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     *
     * @return $this
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }
}
