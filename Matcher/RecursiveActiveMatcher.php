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

use Integrated\Bundle\MenuBundle\Document\Menu;
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
     * @param Menu $menu
     */
    public function setActive(Menu $menu)
    {
        foreach ($menu->getChildren() as $child) {
            // Parent
            if ($this->voter->isCurrent($child)) {
                $child->setCurrent(true);
            }

            // Any subs
            $this->recursiveVote($child);
        }
    }

    /**
     * @param MenuItem $menuItem
     * @return bool
     */
    protected function recursiveVote(MenuItem $menuItem)
    {
        foreach ($menuItem->getChildren() as $child) {
            // Check the actives
            $active = false;
            $active = $this->recursiveVote($child) ? true : $active;
            $active = $this->voter->isCurrent($child) ? true : $active;

            // We active?
            if ($active) {
                $child->setCurrent(true);

                if ($parent = $child->getParent()) {
                    $parent->setCurrent(true);
                }

                // Time to leave this recursive party
                return true;
            }
        }

        return false;
    }

}
