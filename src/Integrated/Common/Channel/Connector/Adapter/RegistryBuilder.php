<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Adapter;

use Integrated\Common\Channel\Connector\AdapterInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RegistryBuilder implements RegistryBuilderInterface
{
    /**
     * @var AdapterInterface[]
     */
    private $adapters = [];

    /**
     * {@inheritdoc}
     */
    public function addAdapter(AdapterInterface $adapter)
    {
        $this->adapters[$adapter->getManifest()->getName()] = $adapter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addAdapters(array $adapters)
    {
        foreach ($adapters as $adapter) {
            $this->addAdapter($adapter);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegistry()
    {
        return new Registry($this->adapters);
    }
}
