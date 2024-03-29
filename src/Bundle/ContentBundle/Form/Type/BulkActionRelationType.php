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

use Integrated\Bundle\ContentBundle\Document\Bulk\Action\RelationAction;
use Integrated\Common\Content\Relation\RelationInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class BulkActionRelationType extends AbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $routeParams;

    public function __construct(UrlGeneratorInterface $generator, string $route, array $routeParams = [])
    {
        $this->generator = $generator;
        $this->route = $route;
        $this->routeParams = $routeParams;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'references',
            BulkActionRelationReferencesType::class,
            [
                'label' => $options['label'],
                'attr' => [
                    'data-id' => $options['relation']->getId(),
                    'data-url' => $this->generator->generate($this->route, $this->routeParams),
                    'class' => 'relation-items',
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['relation', 'relation_handler'])
            ->setAllowedTypes('relation', RelationInterface::class)
            ->setAllowedTypes('relation_handler', 'string')
            ->setDefault('data_class', RelationAction::class)
            ->setDefault('empty_data', function (Options $options) {
                $action = new RelationAction();

                $action->setRelation($options['relation']);
                $action->setHandler($options['relation_handler']);

                return $action;
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_bulk_action_relation';
    }
}
