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

use Exception;
use Integrated\Bundle\ChannelBundle\Form\DataTransformer\OptionsTransformer;
use Integrated\Common\Channel\Connector\Adapter\RegistryInterface;
use Integrated\Common\Channel\Connector\AdapterInterface;
use Integrated\Common\Channel\Connector\ConfigurableInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigFormType extends AbstractType
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var AdapterInterface | ConfigurableInterface $adapter */
        $adapter = $options['adapter'];

        $builder->add('name', TextType::class, [
            'label' => 'form.config.name',
            'translation_domain' => 'IntegratedChannelBundle',
        ]);

        $builder->add('channels', ChannelChoiceType::class, [
            'label' => 'form.config.channels',
            'translation_domain' => 'IntegratedChannelBundle',
            'multiple' => true,
            'expanded' => true,
        ]);

        if ($adapter instanceof ConfigurableInterface) {
            $child = $builder->create(
                'options',
                $adapter->getConfiguration()->getForm(),
                ['label' => 'form.config.options', 'translation_domain' => 'IntegratedChannelBundle']
            );
            $child->addModelTransformer(new OptionsTransformer());

            $builder->add($child);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // Add some extra block prefixes to the options options view. This will
        // allow for more templating options for the options field.

        if (isset($view->children['options']) && $view->children['options']->count()) {
            $view = $view->children['options'];
            $name = $view->vars['original_type'];

            $blocks = [];

            foreach ($view->vars['block_prefixes'] as $prefix) {
                if ($prefix == $name) {
                    $blocks[] = 'channel_options';
                    $blocks[] = 'integrated_channel_options';
                }

                $blocks[] = $prefix;
            }

            $view->vars['block_prefixes'] = $blocks;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $adapterNormalizer = function (Options $options, $adapter) {
            if (\is_string($adapter)) {
                try {
                    $adapter = $this->registry->getAdapter($adapter);
                } catch (Exception $e) {
                    $adapter = null;
                }
            }

            if (!$adapter instanceof AdapterInterface) {
                throw new InvalidOptionsException(sprintf(
                    'The option "%s" could not be normalized to a valid "%s" object',
                    'adapter',
                    'Integrated\\Common\\Channel\\Connector\\AdapterInterface'
                ));
            }

            return $adapter;
        };

        $resolver->setRequired('adapter');
        $resolver->setAllowedTypes('adapter', ['string', 'Integrated\\Common\\Channel\\Connector\\AdapterInterface']);
        $resolver->setNormalizer('adapter', $adapterNormalizer);

        $resolver->setDefault('data_class', 'Integrated\\Bundle\\ChannelBundle\\Model\\Config');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_channel_config';
    }
}
