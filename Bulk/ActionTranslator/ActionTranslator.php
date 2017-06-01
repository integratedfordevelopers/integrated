<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk\ActionTranslator;

use Integrated\Bundle\ContentBundle\Bulk\ActionInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
abstract class ActionTranslator implements ActionTranslatorInterface
{
    /**
     * @var ActionInterface
     */
    protected $action;

    /**
     * @var array
     */
    protected $actionOptions = [];

    /**
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param ActionInterface $action
     * @return $this
     */
    public function setAction(ActionInterface $action)
    {
        $this->action = $action;
        $this->actionOptions = $action->getOptions();
        return $this;
    }

    /**
     * @return string
     */
    protected function getActionName()
    {
        return $this->action->getName();
    }

    /**
     * @return array
     */
    protected function getActionOptions()
    {
        return $this->actionOptions;
    }

    /**
     * @param $optionName
     * @return mixed|null
     */
    protected function getActionOption($optionName)
    {
        if (isset($this->actionOptions[$optionName])) {
            return $this->actionOptions[$optionName];
        }

        return null;
    }
}