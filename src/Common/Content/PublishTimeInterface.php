<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content;

use DateTime;

interface PublishTimeInterface
{
    /**
     * @var string
     */
    public const DATE_MAX = '9999-12-31 00:00:00Z'; // @todo find a better way (INTEGRATED-429)

    /**
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime;

    /**
     * @param DateTime $startDate
     *
     * @return $this
     */
    public function setStartDate(DateTime $startDate = null);

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime;

    /**
     * @param DateTime $endDate
     *
     * @return $this
     */
    public function setEndDate(DateTime $endDate = null);

    /**
     * @param DateTime $date
     *
     * @return bool
     */
    public function isPublished(DateTime $date = null): bool;
}
