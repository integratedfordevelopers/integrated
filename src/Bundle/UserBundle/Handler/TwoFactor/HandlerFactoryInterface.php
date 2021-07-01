<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Handler\TwoFactor;

use Integrated\Bundle\UserBundle\Security\TwoFactor\Http\Context;

interface HandlerFactoryInterface
{
    public function create(Context $context): HandlerInterface;
}
