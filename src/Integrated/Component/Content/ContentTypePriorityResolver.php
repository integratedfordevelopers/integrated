<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Component\Content;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypePriorityResolver implements ContentTypeResolverInterface
{
	private $resolvers = array();
	private $sorted = null;

	/**
	 * Add a resolver to the priority list
	 *
	 * If the resolver is already in the list then if will first be removed and
	 * then added with the new priority
	 *
	 * @param ContentTypeResolverInterface $resolver
	 * @param int                          $priority
	 */
	public function addResolver(ContentTypeResolverInterface $resolver, $priority = 0)
	{
		$this->removeResolver($resolver);

		$this->resolvers[$priority][] = $resolver;
		$this->sorted = null;
	}

	/**
	 * Check is a resolver is added to the priority list
	 *
	 * @param ContentTypeResolverInterface $resolver The resolver to check
	 * @return bool
	 */
	public function hasResolver(ContentTypeResolverInterface $resolver)
	{
		foreach ($this->resolvers as $resolvers) {
			if (false !== array_search($resolver, $resolvers, true)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Remove a resolver from the priority list
	 *
	 * @param ContentTypeResolverInterface $resolver The resolver to remove
	 */
	public function removeResolver(ContentTypeResolverInterface $resolver)
	{
		foreach ($this->resolvers as $priority => $resolvers) {
			if (false !== ($key = array_search($resolver, $resolvers, true))) {
				unset($this->resolvers[$priority][$key]);

				$this->sorted = null;
			}
		}
	}

	/**
	 * Get all the registered resolvers
	 *
	 * @return ContentTypeResolverInterface[]
	 */
	public function getResolvers()
	{
		if ($this->sorted === null) {
			ksort($this->resolvers);
			$this->sorted = call_user_func_array('array_merge', $this->resolvers);
		}

		return $this->sorted;
	}

	/**
	 * Clear all the resolvers
	 */
	public function clearResolvers()
	{
		$this->resolvers = array();
		$this->sorted = null;
	}

	/**
	 * @param string $class
	 * @param string $type
	 *
	 * @return ContentTypeResolverInterface
	 */
	private function findResolver($class, $type)
	{
		foreach ($this->getResolvers() as $resolver) {
			if ($resolver->hasType($class, $type)) {
				return $resolver;
			}
		}

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType($class, $type)
	{
		if ($resolver = $this->findResolver($class, $type))
		{
			return $resolver->getType($class, $type);
		}

		return null; // todo probably change to throwing a exception
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasType($class, $type)
	{
		if ($resolver = $this->findResolver($class, $type))
		{
			return $resolver->hasType($class, $type);
		}

		return false;
	}
}