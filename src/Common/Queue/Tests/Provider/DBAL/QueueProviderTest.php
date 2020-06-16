<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Queue\Tests\Provider\DBAL;

use Doctrine\DBAL\Connection;
use Integrated\Common\Queue\Provider\DBAL\QueueProvider;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueProviderTest extends \PHPUnit\Framework\TestCase
{
    const PAYLOAD = 'O:8:"stdClass":0:{}'; // serialized stdClass;

    /**
     * @var QueueProvider
     */
    protected $provider;

    /**
     * @var Connection | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connection;

    protected function setUp(): void
    {
        $options = [
            'queue_table_name' => 'queue',
        ];

        $this->connection = $this->createMock('Doctrine\DBAL\Connection');
        $this->provider = new QueueProvider($this->connection, $options);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Queue\Provider\QueueProviderInterface', $this->provider);
    }

    public function testPush()
    {
        $this->connection->expects($this->once())
            ->method('insert')
            ->with($this->identicalTo('queue'));

        $this->provider->push('channel', new stdClass());
    }
}
