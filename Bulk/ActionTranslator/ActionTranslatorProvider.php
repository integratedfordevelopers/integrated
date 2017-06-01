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

use Doctrine\Common\Collections\Collection;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class ActionTranslatorProvider
{
    /**
     * @var ActionTranslatorFactory
     */
    private $translatorFactory;

    /**
     * @param ActionTranslatorFactory $translatorFactory
     */
    public function __construct(ActionTranslatorFactory $translatorFactory)
    {
        $this->translatorFactory = $translatorFactory;
    }

    /**
     * @param Collection $collection
     * @return array
     */
    public function getTranslators(Collection $collection)
    {
        $translators = [];

        foreach ($collection->getIterator() as $action) {
            $translators[] = $this->translatorFactory->getActionTranslator($action);
        }

        return $translators;
    }
}