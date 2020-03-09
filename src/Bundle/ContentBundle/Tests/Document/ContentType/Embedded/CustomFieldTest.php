<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\ContentType\Embedded;

use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CustomFieldTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CustomField
     */
    private $field;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->field = new CustomField();
    }

    /**
     * Test instanceof.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field', $this->field);
    }
}
