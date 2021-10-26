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

use Doctrine\Persistence\ObjectRepository;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class DefinitionTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectRepository
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
        return $this->repository->findOneBy(['id' => $value]);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if ($value instanceof Definition) {
            return $value->getId();
        }

        return null;
    }
}
