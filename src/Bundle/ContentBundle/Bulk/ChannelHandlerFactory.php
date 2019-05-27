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

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Common\Bulk\Action\HandlerFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

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
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * Constructor.
     *
     * @param string               $class
     * @param DocumentManager      $documentManager
     * @param AuthorizationChecker $authorizationChecker
     */
    public function __construct($class, DocumentManager $documentManager, AuthorizationChecker $authorizationChecker)
    {
        $this->class = $class;

        $this->resolver = new OptionsResolver();
        $this->resolver
            ->setRequired(['channel'])
            ->addAllowedTypes('channel', 'string');

        $this->documentManager = $documentManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function createHandler(array $options)
    {
        $options = $this->resolver->resolve($options);
        $class = $this->class;

        return new $class($options['channel'], $this->documentManager->getRepository(Channel::class), $this->authorizationChecker);
    }
}
