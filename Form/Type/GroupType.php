<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Form\Type;

use Integrated\Bundle\UserBundle\Model\GroupManagerInterface;

use Integrated\Common\Form\DataTransformer\ValuesToChoicesTransformer;
use Integrated\Common\Form\DataTransformer\ValueToChoiceTransformer;

use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class GroupType extends AbstractType
{
    /**
     * @var GroupManagerInterface
     */
    private $manager;

    /**
     * @param GroupManagerInterface $manager
     */
    public function __construct(GroupManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['choice_data'] == 'scalar') {
            if ($options['multiple']) {
                // entity adds the CollectionToArrayTransformer and that is fine but we need
                // to insert a transformer just after that one. That how ever is not possible
                // so the CollectionToArrayTransformer is first removed and then later added
                // again after our transformer is added.

                $transformers = [];

                foreach ($builder->getViewTransformers() as $transformer) {
                    if (!$transformer instanceof CollectionToArrayTransformer) {
                        $transformers[] = $transformer;
                    }
                }

                $builder->resetViewTransformers();

                foreach ($transformers as $transformer) {
                    $builder->addViewTransformer($transformer);
                }

                $builder->addViewTransformer(new ValuesToChoicesTransformer($options['choice_list']), true);
                $builder->addViewTransformer(new CollectionToArrayTransformer(), true);
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
        $resolver->setDefault('class', $this->manager->getClassName());

        $resolver->setDefault('choice_data', 'object');
        $resolver->setDefault('choice_value', 'id');
        $resolver->setDefault('choice_label', 'name');

        $resolver->addAllowedValues('choice_data', ['object', 'scalar']);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return EntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_user_group_choice';
    }

    /**
     * @return GroupManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }
}
