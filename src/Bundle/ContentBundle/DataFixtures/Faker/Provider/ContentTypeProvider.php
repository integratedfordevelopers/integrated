<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\DataFixtures\Faker\Provider;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;

class ContentTypeProvider
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @param string $id
     * @return ContentType
     * @throws DocumentNotFoundException
     */
    public function contentType($id)
    {
        $contentType = $this->dm->getRepository(ContentType::class)->find($id);

        if (!$contentType) {
            throw DocumentNotFoundException::documentNotFound(ContentType::class, $id);
        }

        return $contentType;
    }
}
