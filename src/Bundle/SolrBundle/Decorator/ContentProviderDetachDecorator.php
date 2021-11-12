<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Decorator;

use Doctrine\Persistence\ObjectManager;
use Integrated\Bundle\SolrBundle\Iterator\DetachIterator;
use Integrated\Common\Solr\Task\Provider\ContentProviderInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class ContentProviderDetachDecorator implements ContentProviderInterface
{
    /**
     * @var ContentProviderInterface
     */
    private $provider;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @param ContentProviderInterface $provider
     * @param ObjectManager            $manager
     */
    public function __construct(ContentProviderInterface $provider, ObjectManager $manager)
    {
        $this->provider = $provider;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenced($id)
    {
        return new DetachIterator($this->provider->getReferenced($id), $this->manager);
    }
}
