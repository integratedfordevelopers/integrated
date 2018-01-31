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

use Integrated\Bundle\ContentBundle\Document\Channel\ChannelRepository;
use Integrated\Common\Channel\ChannelInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ChannelTransformer implements DataTransformerInterface
{
    /**
     * @var ChannelRepository
     */
    private $repository;

    /**
     * @param ChannelRepository $repository
     */
    public function __construct(ChannelRepository $repository)
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

        return $this->repository->findByIds($value);
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
