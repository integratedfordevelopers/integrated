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

/**
 * Interface for Channel documents
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
interface ChannelInterface
{
    /**
     * Return the id of the Channel document
     *
     * @return string
     */
    public function getId();

    /**
     * Return the name of the Channel document
     *
     * @return string
     */
    public function getName();

    /**
     * Return the short name of the Channel document
     *
     * @return string
     */
    public function getShortName();
}