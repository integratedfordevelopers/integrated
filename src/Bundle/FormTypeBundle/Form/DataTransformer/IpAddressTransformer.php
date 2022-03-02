<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\DataTransformer;

use Darsyn\IP\Exception\IpException;
use Darsyn\IP\Version\Multi as IP;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IpAddressTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value) {
            return null;
        } elseif ($value instanceof IP) {
            return $value->getProtocolAppropriateAddress();
        }

        throw new TransformationFailedException(sprintf('Expected %s, "%s" given', IP::class, \gettype($value)));
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        } elseif (\is_string($value)) {
            try {
                return IP::factory($value);
            } catch (IpException $e) {
                throw new TransformationFailedException($e->getMessage(), 0, $e);
            }
        }

        throw new TransformationFailedException(sprintf('Expected string, "%s" given', \gettype($value)));
    }
}
