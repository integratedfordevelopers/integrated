<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Adaptor;

use Integrated\Common\Channel\Connector\AdaptorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface RegistryBuilderInterface
{
    /**
     * @param AdaptorInterface $factory
     *
     * @return RegistryBuilderInterface
     */
    public function addAdaptor(AdaptorInterface $factory);

    /**
     * @param AdaptorInterface[] $factories
     *
     * @return RegistryBuilderInterface
     */
    public function addAdaptors(array $factories);

    /**
     * @return RegistryInterface
     */
    public function getRegistry();
}
