<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form\Event;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\Mapping\MetadataInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class OptionsEvent extends FormEvent
{
	/**
	 * @var OptionsResolverInterface
	 */
	private $resolver;

	public function __construct(ContentTypeInterface $contentType, MetadataInterface $metadata, OptionsResolverInterface $resolver)
	{
		parent::__construct($contentType, $metadata);

		$this->resolver = $resolver;
	}

	/**
	 * @return OptionsResolverInterface
	 */
	public function getResolver()
	{
		return $this->resolver;
	}
}