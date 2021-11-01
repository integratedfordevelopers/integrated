<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SocialBundle\Factory;

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class TwitterFactory
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;

    /**
     * @param string $key
     * @param string $secret
     */
    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * @param string|null $token
     * @param string|null $secret
     *
     * @return TwitterOAuth
     */
    public function createClient($token = null, $secret = null)
    {
        return new TwitterOAuth($this->key, $this->secret, $token, $secret);
    }
}
