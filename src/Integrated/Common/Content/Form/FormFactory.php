<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form;

use Integrated\Common\Content\Exception\UnexpectedTypeException;
use Integrated\Common\Content\ContentInterface;

use Integrated\Common\ContentType\Mapping\MetadataFactoryInterface;
use Integrated\Common\ContentType\ResolverInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FormFactory implements FormFactoryInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadata;

	/**
	 * @var EventDispatcherInterface
	 */
	private $dispatcher = null;

    /**
     * @param ResolverInterface        $resolver
     * @param MetadataFactoryInterface $metadata
     */
    public function __construct(ResolverInterface $resolver, MetadataFactoryInterface $metadata)
	{
		$this->resolver = $resolver;
        $this->metadata = $metadata;
	}

	/**
	 * @param EventDispatcherInterface $dispatcher
	 */
	public function setEventDispatcher(EventDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	 * @return EventDispatcherInterface
	 */
	public function getEventDispatcher()
	{
		if ($this->dispatcher === null) {
			$this->dispatcher = new EventDispatcher();
		}

		return $this->dispatcher;
	}

    /**
   	 * {@inheritdoc}
   	 */
    public function getType($type)
    {
        if ($type instanceof ContentInterface) {
            $type = $type->getContentType();
        }

        if (!is_string($type)) {
            throw new UnexpectedTypeException($type, 'string or Integrated\\Common\\Content\\ContentInterface');
        }

        return new FormType($type = $this->resolver->getType($type), $this->metadata->getMetadata($type->getClass()), $this->getEventDispatcher());
    }
}
