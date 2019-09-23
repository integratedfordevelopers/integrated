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

trait RankTrait
{
    /**
     * @var string|null
     */
    protected $rank;

    /**
     * @return string|null
     */
    public function getRank(): ? string
    {
        return $this->rank;
    }

    /**
     * @param string|null $rank
     */
    public function setRank(string $rank = null)
    {
        $this->rank = $rank;
    }
}
