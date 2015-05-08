<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Exporter\Queue;

use Exception;

use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\ChannelManagerInterface;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Util\ClassUtils;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RequestSerializer implements RequestSerializerInterface
{
    /**
   	 * @var SerializerInterface
   	 */
   	private $serializer;

    /**
     * @var ChannelManagerInterface
     */
    private $manager;

    /**
     * Constructor.
     *
     * @param SerializerInterface     $serializer
     * @param ChannelManagerInterface $manager
     */
    public function __construct(SerializerInterface $serializer, ChannelManagerInterface $manager)
    {
        $this->serializer = $serializer;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(Request $data)
    {
        return json_encode([
            'content' => [
                'data' => $this->serializer->serialize($data->content, 'json'),
                'type' => ClassUtils::getRealClass($data->content)
            ],
            'state' => $data->state,
            'channel' => $data->channel instanceof ChannelInterface ? $data->channel->getId() : null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($data)
    {
        $data = json_decode($data, true);

        if (!is_array($data) || empty($data['content']) || empty($data['content']['data']) || empty($data['content']['type']) || empty($data['state']) || empty($data['channel'])) {
            return null;
        }

        $request = new Request();

        try {
            $request->content = $this->serializer->deserialize($data['content']['data'], $data['content']['type'], 'json');
            $request->state = (string) $data['state'];
            $request->channel = $this->manager->find($data['channel']);
        } catch (Exception $e) {
            return null;
        }

        // only return a valid none empty request object

        if ($request->content && $request->channel instanceof ChannelInterface) {
            return $request;
        }

        return null;
    }
}
