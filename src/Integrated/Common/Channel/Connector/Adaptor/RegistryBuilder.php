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
class RegistryBuilder implements RegistryBuilderInterface
{
    /**
     * @var AdaptorInterface[]
     */
    private $adaptors = [];

    /**
     * {@inheritdoc}
     */
    public function addAdaptor(AdaptorInterface $adaptor)
    {
        $this->adaptors[$adaptor->getManifest()->getName()] = $adaptor;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addAdaptors(array $adaptors)
    {
        foreach ($adaptors as $adaptor) {
            $this->addAdaptor($adaptor);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegistry()
    {
        return new Registry($this->adaptors);
    }
}
