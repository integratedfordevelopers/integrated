<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Form\ChoiceList;

use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChannelChoiceLoader implements ChoiceLoaderInterface
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var ChoiceListFactoryInterface
     */
    private $factory;

    /**
     * Cached version of the choice list.
     *
     * @var ChoiceListInterface
     */
    private $choiceList = null;

    /**
     * Constructor.
     *
     * @param ObjectRepository           $repository
     * @param ChoiceListFactoryInterface $factory
     */
    public function __construct(ObjectRepository $repository, ChoiceListFactoryInterface $factory)
    {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        if ($this->choiceList) {
            return $this->choiceList;
        }

        return $this->choiceList = $this->factory->createListFromChoices($this->repository->findAll(), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        if (!$values) {
            return [];
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    /**
     * {@inheritdoc}
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        if (!$choices) {
            return [];
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }
}
