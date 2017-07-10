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

use Integrated\Bundle\ContentBundle\Form\EventListener\BulkActionsMapperListener;
use Integrated\Common\Bulk\Form\ConfigProviderInterface;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class BulkActionsType extends AbstractType
{
    /**
     * @var ConfigProviderInterface
     */
    private $provider;

    /**
     * @param ConfigProviderInterface $provider
     */
    public function __construct(ConfigProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $mapping = [];

        foreach ($this->provider->getConfig($options['content']) as $config) {
            $builder->add(
                $name = sprintf('%s_%s', bin2hex($config->getHandler()), $config->getName()),
                BulkActionType::class,
                [
                    'config' => $config,
                    'mapped' => false,
                ]
            );

            $mapping[$config->getHandler()][] = [
                'matcher' => $config->getMatcher(),
                'name' => $name
            ];
        }

        $builder->addEventSubscriber(new BulkActionsMapperListener($mapping, $options['readonly']));
    }

    /**
     * {@inheritDoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (!$options['readonly']) {
            return;
        }

        foreach ($view->children as $name => $child) {
            if (!$child->children['active']->vars['data']) {
                unset($view->children[$name]);
            }
        }

        $this->updateReadonlyView($view);
    }

    /**
     * @param FormView $view
     */
    private function updateReadonlyView(FormView $view)
    {
        foreach ($view->children as $child) {
            $last = array_pop($child->vars['block_prefixes']);

            $child->vars['block_prefixes'][] = sprintf('%s_readonly', end($child->vars['block_prefixes']));
            $child->vars['block_prefixes'][] = $last;
            $child->vars['block_prefixes'][] = sprintf('%s_readonly', $last);

            if ($child->children) {
                $this->updateReadonlyView($child);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('readonly', false)
            ->setAllowedTypes('readonly', 'bool')

            ->setRequired('content')
            ->setAllowedTypes('content', 'array')
            ->setAllowedValues('content', function (array $content) {
                foreach ($content as $item) {
                    if (!$item instanceof ContentInterface) {
                        return false;
                    }
                }

                return true;
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_bulk_actions';
    }
}
