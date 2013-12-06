<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Indexer;

use Serializable;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface JobInterface extends Serializable
{
	/**
	 * Get the action.
	 *
	 * @return string|null
	 */
	public function getAction();

	/**
	 * Check if action is set.
	 *
	 * @return bool
	 */
	public function hasAction();

	/**
	 * Get the option value.
	 *
	 * @param string $name the name of the option to retrieve.
	 * @return string|null
	 */
	public function getOption($name);

	/**
	 * Check if the option exists.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasOption($name);
}