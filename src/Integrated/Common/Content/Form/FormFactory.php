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
use Integrated\Common\Content\Exception\InvalidArgumentException;
use Integrated\Common\Content\ContentInterface;

use Integrated\Common\ContentType\Mapping\MetadataFactory;
use Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FormFactory implements FormFactoryInterface
{
    /**
     * @var \Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface
     */
    private $resolver;

    /**
     * @var MetadataFactory
     */
    private $metadata;

    /**
     * @param ContentTypeResolverInterface $resolver
     * @param MetadataFactory $metadata
     */
    public function __construct(ContentTypeResolverInterface $resolver, MetadataFactory $metadata)
	{
		$this->resolver = $resolver;
        $this->metadata = $metadata;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType($class, $type = null)
	{
		if ($type === null && $class instanceof ContentInterface) {
			$type = $class->getContentType();
		}

		if ($class instanceof ContentInterface) {
			$class = get_class($class);
		} elseif (!is_string($class)) {
			throw new UnexpectedTypeException($class, 'string or Integrated\Common\Content\ContentInterface');
		} elseif (!class_exists($class) || !is_subclass_of($class, 'Integrated\Common\Content\ContentInterface')) {
			throw new InvalidArgumentException(sprintf('The content class "%s" is not a valid class or not subclass of Integrated\Common\Content\ContentInterface.', $class));
		}

		if (!is_string($type)) {
			throw new UnexpectedTypeException($type, 'string');
  		}

		return new FormType($this->resolver->getType($class, $type), $this->metadata);
	}
}