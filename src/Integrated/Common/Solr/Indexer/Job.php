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
class Job implements Serializable
{
	/**
	 * @var string
	 */
	private $action;

	/**
	 * @var string[]
	 */
	private $options;

	/**
	 * @param null $action
	 * @param array $options
	 */
	public function __construct($action = null, array $options = array())
	{
		$this->setAction($action);

		foreach ($options as $name => $value) {
			$this->setOption($name, $value);
		}
	}

	public function serialize()
	{
		return serialize(array('action' => $this->action, 'options' => $this->options));
	}

	public function unserialize($serialized)
	{
		$data = array();

		list(
			$data['action'],
			$data['options'],
		) = unserialize($serialized);

		$this->__construct($data['action'], $data['options']);
	}

	/**
	 * @param string $action
	 * @return $this
	 */
	public function setAction($action)
	{
		$this->action = $action === null ? $action : (string) $action;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return $this
	 */
	public function setOption($name, $value)
	{
		$this->options[$name] = (string) $value;
		return $this;
	}

	/**
	 * @param string $name
	 * @return string|null
	 */
	public function getOption($name)
	{
		return isset($this->options[$name]) ? $this->options[$name] : null;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasOption($name)
	{
		return isset($this->options[$name]);
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function removeOption($name)
	{
		unset($this->options[$name]);
		return $this;
	}

	/**
	 * @return string[]
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @return $this
	 */
	public function clearOptions()
	{
		$this->options = array();
		return $this;
	}
}