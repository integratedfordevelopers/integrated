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
     * @var bool
     */
    private $multiple;

    /**
     * @param ChannelRepository $repository
     * @param bool              $multiple
     */
    public function __construct(ChannelRepository $repository, $multiple = false)
    {
        $this->repository = $repository;
        $this->multiple = $multiple;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (!$this->multiple) {
            return $this->repository->findOneBy(['id' => $value]);
        }

        if (!\is_array($value)) {
            return [];
        }

        return $this->repository->findByIds($value);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!$this->multiple) {
            if ($value instanceof ChannelInterface) {
                return $value->getId();
            }

            return null;
        }

        if (!\is_array($value)) {
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
