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
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChannelHandlerFactory implements HandlerFactoryInterface
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
            ->setRequired(['channel'])
            ->addAllowedTypes('channel', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function createHandler(array $options)
    {
        $options = $this->resolver->resolve($options);
        $class = $this->class;

        return new $class($options['channel']);
    }
}
