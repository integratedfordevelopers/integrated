<?php

/**
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Integrated\Common\Bulk\Form\ConfigInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BulkActionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ConfigInterface $config */
        $config = $options['config'];

        $label = $config->getOptions();
        $label = isset($label['label']) ? $label['label'] : $config->getName();

        $builder->add('active', CheckboxType::class, ['label' => $label, 'required' => false]);
        $builder->add('action', $config->getType(), $config->getOptions());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('config')
            ->setAllowedTypes('config', ConfigInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_bulk_action';
    }
}
