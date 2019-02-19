<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypeControllerManager
{
    /**
     * @var ArrayCollection
     */
    private $controllers;

    /**
     * ContentTypeControllerManager constructor.
     */
    public function __construct()
    {
        $this->controllers = new ArrayCollection();
    }

    /**
     * @param $serviceId
     * @param $attributes
     *
     * @throws \Exception
     */
    public function addController($serviceId, $attributes)
    {
        if (!\array_key_exists('class', $attributes)) {
            throw new \InvalidArgumentException(
                sprintf('class is a required attribute of the tag in service "%s"', $serviceId)
            );
        }

        $className = $attributes['class'];

        if ($this->controllers->containsKey($className)) {
            throw new \Exception(
                sprintf('You can only define one content controller service for class "%s"', $className)
            );
        }

        if (\array_key_exists('controller_actions', $attributes)) {
            $controllerActions = array_map('trim', explode(',', $attributes['controller_actions']));
        } else {
            $controllerActions = ['showAction'];
        }

        $this->controllers->set($className, [
            'service' => $serviceId,
            'class_name' => $className,
            'controller_actions' => $controllerActions,
        ]);
    }

    /**
     * @param $className
     *
     * @return string|null
     */
    public function getController($className)
    {
        return $this->controllers->get($className);
    }
}
