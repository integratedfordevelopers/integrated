<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectRepository;

use Integrated\Bundle\ChannelBundle\Form\ChoiceList\ChannelChoiceLoader;

use Integrated\Common\Form\DataTransformer\ValuesToChoicesTransformer;
use Integrated\Common\Form\DataTransformer\ValueToChoiceTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Factory\CachingFactoryDecorator;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory;
use Symfony\Component\Form\ChoiceList\Factory\PropertyAccessDecorator;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChannelChoiceType extends AbstractType
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var PropertyAccessorInterface
     */
    private $accessor;

    /**
     * @var ChoiceListFactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param ObjectRepository           $repository
     * @param PropertyAccessorInterface  $accessor
     * @param ChoiceListFactoryInterface $factory
     */
    function __construct(ObjectRepository $repository, PropertyAccessorInterface $accessor = null, ChoiceListFactoryInterface $factory = null)
    {
        $this->repository = $repository;

        $this->accessor = $accessor ?: PropertyAccess::createPropertyAccessor();
        $this->factory = $factory ?: new CachingFactoryDecorator(new PropertyAccessDecorator(new DefaultChoiceListFactory(), $this->accessor));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['choice_data'] == 'scalar') {
            if ($options['multiple']) {
                $builder->addViewTransformer(new ValuesToChoicesTransformer($options['choice_list']), true);
            } else {
                $builder->addViewTransformer(new ValueToChoiceTransformer($options['choice_list']), true);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // The choice loader is forced to a ChannelChoiceLoader since this is a channel choice
        // form type so what else would you expect.

        $choiceLoaderNormalizer = function(Options $options) {
            return new ChannelChoiceLoader($this->repository, $this->factory);
        };

        $resolver->setNormalizer('choice_loader', $choiceLoaderNormalizer);

        $resolver->setDefault('choice_data', 'object');
        $resolver->setDefault('choice_value', 'id');
        $resolver->setDefault('choice_label', 'name');

        $resolver->setAllowedValues('choice_data', ['object', 'scalar']);
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_channel_choice';
    }
}
