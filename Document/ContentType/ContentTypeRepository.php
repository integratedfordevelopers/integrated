<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\ContentType;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Integrated\Common\ContentType\ContentTypeRepositoryInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeRepository extends DocumentRepository implements ContentTypeRepositoryInterface
{
}
