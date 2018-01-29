<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Integrated\Common\Channel\ChannelInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ChannelTransformer implements DataTransformerInterface
{
    /**
     * @var ManagerRegistry
     */
    private $repository;

    /**
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (!is_array($value)) {
            return [];
        }

        $values = [];
        foreach ($value as $channel) {
            $values[] = $this->repository->findOneBy(['id' => $channel]);
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!is_array($value)) {
            return [];
        }

        $values = [];
        foreach ($value as $channel) {
            if (!$channel instanceof ChannelInterface) {
                continue;
            }

            $values[] = $channel->getId();
        }

        return $values;
    }
}
