<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\MenuBundle\Matcher;

use Integrated\Bundle\MenuBundle\Document\MenuItem;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class RecursiveActiveMatcher
{
    /**
     * @var Matcher
     */
    protected $voter;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        // Create a knp menu voter
        $this->voter = new Matcher();
        $this->voter->addVoter(
            new UriVoter(
                str_replace(
                    $requestStack->getMasterRequest()->getScriptName(), // contains; app.php or app_dev.php
                    '',                                                 // info not needed for voting
                    $requestStack->getMasterRequest()->getRequestUri()  // request uri
                )
            )
        );
    }

    /**
     * @param MenuItem $menuItem
     */
    public function setActive(MenuItem $menuItem)
    {
        foreach ($menuItem->getChildren() as $item) {
            // Run recursive, find any children playing this game
            $this->setActive($item);

            // We active?
            if ($this->voter->isCurrent($item)) {
                $item->setCurrent(true);

                // Run trough any parent items
                $parent = $item;
                while ($parent->getParent()) {
                    $parent->setCurrent(true);
                    $parent = $parent->getParent();
                }
            }
        }
    }

}
