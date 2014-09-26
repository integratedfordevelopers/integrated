<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Form\DataTransformer;

use ArrayObject;

use Integrated\Bundle\WorkflowBundle\Entity\Definition;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DefinitionTransformer implements DataTransformerInterface
{
    /**
   	 * {@inheritdoc}
   	 */
	public function transform($value)
	{
		if ($value === null || $value === '') {
			return null;
		}

		if (!is_string($value) && !is_int($value)) {
			throw new TransformationFailedException('Expected a string or integer.');
		}

		return new ArrayObject(['id' => $value], ArrayObject::ARRAY_AS_PROPS);
	}

    /**
   	 * {@inheritdoc}
   	 */
	public function reverseTransform($value)
	{
		if ($value === null || $value === '') {
			return null;
		}

		if (!$value instanceof Definition) {
			throw new TransformationFailedException('Expected a Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition object.');
		}

		return $value->getId();
	}
}