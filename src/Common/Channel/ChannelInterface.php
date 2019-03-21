<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel;

use Integrated\Common\Security\PermissionInterface;

/**
 * Interface for Channel documents.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
interface ChannelInterface
{
    /**
     * Return the id of the Channel.
     *
     * @return string
     */
    public function getId();

    /**
     * Return the name of the Channel.
     *
     * @return string
     */
    public function getName();

    /**
     * @return PermissionInterface[]
     */
    public function getPermissions();

    /**
     * @return string
     */
    public function getPrimaryDomain();

    /**
     * @return bool
     */
    public function getPrimaryDomainRedirect();
}
