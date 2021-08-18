<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Locks\Provider\DBAL;

use DateTime;
use Integrated\Common\Locks\LockInterface;
use Integrated\Common\Locks\RequestInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Lock implements LockInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DateTime
     */
    private $created;

    /**
     * @var DateTime|null
     */
    private $expires;

    /**
     * @param string           $id
     * @param RequestInterface $request
     * @param DateTime         $created
     * @param DateTime         $expires
     */
    public function __construct($id, RequestInterface $request, DateTime $created, DateTime $expires = null)
    {
        $this->id = (string) $id;
        $this->request = $request;
        $this->created = $created;
        $this->expires = $expires;
    }

    /**
     * @param array $data
     *
     * @return self
     */
    public static function factory(array $data)
    {
        $request = new Request(Resource::unserialize($data['resource']), Resource::unserialize($data['resource_owner']), $data['timeout']);

        $created = new DateTime();
        $created->setTimestamp($data['created']);

        $expires = null;

        if ($data['expires']) {
            $expires = new DateTime();
            $expires->setTimestamp($data['expires']);
        }

        return new self($data['id'], $request, $created, $expires);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpires()
    {
        return $this->expires;
    }
}
