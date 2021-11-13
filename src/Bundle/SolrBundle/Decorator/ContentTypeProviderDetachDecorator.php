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
use Integrated\Common\Solr\Task\Provider\ContentTypeProviderInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class ContentTypeProviderDetachDecorator implements ContentTypeProviderInterface
{
    /**
     * @var ContentTypeProviderInterface
     */
    private $provider;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @param ContentTypeProviderInterface $provider
     * @param ObjectManager                $manager
     */
    public function __construct(ContentTypeProviderInterface $provider, ObjectManager $manager)
    {
        $this->provider = $provider;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent($id)
    {
        return new DetachIterator($this->provider->getContent($id), $this->manager);
    }
}
