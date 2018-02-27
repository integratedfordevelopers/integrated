<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Exporter\Queue;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The container aware request serializer will lazy load the dependent services from
 * the container. This could be useful if your having problems with the container
 * builder because of circular references.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContainerAwareRequestSerializer extends RequestSerializer
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string[]
     */
    private $services;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param string             $serializer
     * @param string             $manager
     */
    public function __construct(ContainerInterface $container, $serializer, $manager)
    {
        $this->container = $container;
        $this->services = [
            'serializer' => $serializer,
            'manager' => $manager,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializer()
    {
        if (null === $this->serializer) {
            $this->serializer = $this->container->get($this->services['serializer']);
        }

        return $this->serializer;
    }

    /**
     * {@inheritdoc}
     */
    protected function getManager()
    {
        if (null === $this->manager) {
            $this->manager = $this->container->get($this->services['manager']);
        }

        return $this->manager;
    }
}
