<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\PublishTime;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PublishTimeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('startDate', 'integrated_datetime');
        $builder->add('endDate', 'integrated_datetime');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class'  => 'Integrated\Bundle\ContentBundle\Document\Content\Embedded\PublishTime',

            'constraints' => new Callback(function (PublishTime $publishTime, ExecutionContextInterface $context) {

                $startDate = $publishTime->getStartDate();
                $endDate   = $publishTime->getEndDate();

                if ($startDate instanceof \DateTime && $endDate instanceof \DateTime) {

                    if ($endDate < $startDate) {
                        $context->buildViolation("The end date can't be earlier than the begin date")->atPath('endDate')->addViolation();
                    }
                }
            }),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_publishtime';
    }
}
