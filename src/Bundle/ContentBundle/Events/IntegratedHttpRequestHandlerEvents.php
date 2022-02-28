<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Events;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
final class IntegratedHttpRequestHandlerEvents
{
    /**
     * @const Called before the request handler starts work on the form
     */
    public const PRE_HANDLE = 'pre.handle';

    /**
     * @const Called after the request handler checked the form
     */
    public const POST_HANDLE = 'post.handle';

    /**
     * Instantiation prohibited.
     */
    private function __construct()
    {
    }
}
