<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Task\Tasks;

use Serializable;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ReferenceQueueTask implements Serializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->id = $serialized;
    }

    public function __serialize(): array
    {
        return ['id' => $this->id];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
    }
}
