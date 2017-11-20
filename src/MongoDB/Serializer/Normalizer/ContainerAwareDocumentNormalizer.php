<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\MongoDB\Serializer\Normalizer;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContainerAwareDocumentNormalizer extends DocumentNormalizer
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $service;

    /**
     * @param ContainerInterface $container
     * @param string $service the name of the document manager service to use
     */
    public function __construct(ContainerInterface $container, $service)
    {
        $this->container = $container;
        $this->service = $service;
    }

    /**
     * @inheritdoc
     */
    protected function getDocumentManager()
    {
        if ($this->dm === null) {
            $this->dm = $this->container->get($this->service);
        }

        return $this->dm;
    }
}
