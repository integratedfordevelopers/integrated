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
 * @author Michael Jongman <michael@e-active.nl>
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

    /**
     * @return boolean
     */
    public function isAdmin();

    /**
     * @param boolean $admin
     * @return $this
     */
    public function setAdmin($admin);
}
