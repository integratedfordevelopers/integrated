<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk;

use Integrated\Common\Bulk\Action\HandlerFactoryInterface;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Relation\RelationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Traversable;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RelationHandlerFactory implements HandlerFactoryInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var OptionsResolver
     */
    private $resolver;

    /**
     * Constructor.
     *
     * @param string $class
     */
    public function __construct($class)
    {
        $this->class = $class;

        $this->resolver = new OptionsResolver();
        $this->resolver
            ->setRequired(['relation', 'references'])
            ->addAllowedTypes('relation', RelationInterface::class)
            ->addAllowedTypes('references', [Traversable::class, 'array'])
            ->setAllowedValues('references', function ($content) {
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
    public function createHandler(array $options)
    {
        $options = $this->resolver->resolve($options);
        $class = $this->class;

        if (!\count($options['references'])) {
            $class = RelationNoopHandler::class;
        }

        return new $class($options['relation'], $options['references']);
    }
}
