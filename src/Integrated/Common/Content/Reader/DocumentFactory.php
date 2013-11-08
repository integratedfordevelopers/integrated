<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Reader;

use Doctrine\Common\Persistence\ManagerRegistry;
use Integrated\Common\ContentType\Mapping\Metadata;

/**
 * Factory for creating Document
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class DocumentFactory
{
    /**
     * @param ManagerRegistry $managerRegistry
     * @param Metadata\ContentTypeFactory $contentTypeFactory
     * @return Document
     */
    public function build(ManagerRegistry $managerRegistry, Metadata\ContentTypeFactory $contentTypeFactory)
    {
        return new Document($managerRegistry->getManager()->getMetadataFactory(), $contentTypeFactory);
    }
}