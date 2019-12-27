<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\Content\Embedded;

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Location;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class LocationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Location
     */
    private $location;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->location = new Location();
    }

    /**
     * Test get- and setLatitude function.
     */
    public function testGetAndSetLatitudeFunction()
    {
        $latitude = 1.5;
        $this->assertEquals($latitude, $this->location->setLatitude($latitude)->getLatitude());
    }

    /**
     * Test get- and setLongitude function.
     */
    public function testGetAndSetLongitudeFunction()
    {
        $longitude = 5.5;
        $this->assertEquals($longitude, $this->location->setLongitude($longitude)->getLongitude());
    }
}
