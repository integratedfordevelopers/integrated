<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\DataTransformer;

use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ValuesToChoicesTransformer implements DataTransformerInterface
{
    /**
     * @var ChoiceListInterface
     */
    private $choiceList;

    /**
     * Constructor.
     *
     * @param ChoiceListInterface $choiceList
     */
    public function __construct(ChoiceListInterface $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($values)
    {
        if (null === $values) {
            return [];
        }

        if (!\is_array($values)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return $this->choiceList->getChoicesForValues($values);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($values)
    {
        if (null === $values) {
            return [];
        }

        if (!\is_array($values)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $results = $this->choiceList->getValuesForChoices($values);

        if (\count($results) !== \count($values)) {
            throw new TransformationFailedException('Could not correctly convert all the values');
        }

        return $results;
    }
}
