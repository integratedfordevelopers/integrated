<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk\Action\Translator;

use Integrated\Bundle\ContentBundle\Bulk\Action\ActionInterface;
use Integrated\Bundle\ContentBundle\Bulk\Action\ActionTranslatorInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
abstract class AbstractActionTranslator implements ActionTranslatorInterface
{
    /**
     * @var ActionInterface
     */
    protected $action;

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
        return $this->action->getOptions();
    }

    /**
     * @param $optionName
     * @return mixed|null
     */
    protected function getActionOption($optionName)
    {
        if (key_exists($optionName, $this->action->getOptions())) {
            $options = $this->action->getOptions();
            return $options[$optionName];
        }

        return null;
    }
}
