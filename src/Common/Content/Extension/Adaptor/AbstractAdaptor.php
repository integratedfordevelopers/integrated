<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension\Adaptor;

use Integrated\Common\Content\Extension\AdaptorInterface;
use Integrated\Common\Content\Extension\DispatcherInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
abstract class AbstractAdaptor implements AdaptorInterface
{
    /**
     * @var DispatcherInterface|null
     */
    protected $dispatcher = null;

    /**
     * @param DispatcherInterface $dispatcher
     */
    public function setDispatcher(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return DispatcherInterface|null
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }
}
