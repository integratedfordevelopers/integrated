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
class ActionTranslatorFactory
{
    /**
     * @param ActionInterface $action
     * @return ActionTranslatorInterface
     */
    public function getActionTranslator(ActionInterface $action)
    {
        $reflectionClass = new \ReflectionClass($action);
        $className = "\\Integrated\\Bundle\\ContentBundle\\Bulk\\ActionTranslator\\" . $reflectionClass->getShortName() . 'Translator';
        $actionTranslator = new $className();

        if (!$actionTranslator instanceof AbstractActionTranslator) {
            throw new \RuntimeException(get_class($className) . ' does not extent the ActionTranslator');
        }

        $actionTranslator->setAction($action);

        return $actionTranslator;
    }
}
