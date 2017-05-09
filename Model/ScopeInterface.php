<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Model;

/**
 * Interface ScopeInterface
 * @package Integrated\Bundle\UserBundle\Model
 */
interface ScopeInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param $name
     * @return string
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();
}
