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

use Darsyn\IP\Version\Multi as IP;

class IpList
{
    /**
     * @var string
     */
    protected $id = null;

    /**
     * @var IP
     */
    protected $ip;

    /**
     * @var string
     */
    protected $description;

    public function __construct(IP $ip, string $description = '')
    {
        $this->ip = $ip;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIp(): IP
    {
        return $this->ip;
    }

    /**
     * @param IP $ip
     */
    public function setIp(IP $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
