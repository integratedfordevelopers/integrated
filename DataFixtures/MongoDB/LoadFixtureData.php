<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\DataFixtures\MongoDB;

use Integrated\Bundle\ContentBundle\DataFixtures\MongoDB\LoadFixtureData as FixtureHandler;

use Integrated\Bundle\ChannelBundle\DataFixtures\MongoDB\Extension\ChannelExtension;
use Integrated\Bundle\ContentBundle\DataFixtures\MongoDB\Extension\SearchSelectionExtension;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class LoadFixtureData extends FixtureHandler
{
    use ChannelExtension;
    use SearchSelectionExtension;

    /**
     * @var string
     */
    protected $path = __DIR__;
}
