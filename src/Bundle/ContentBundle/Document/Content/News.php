<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Content;

use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * Document type News.
 *
 * @author Koen Prins <koen@e-active.nl>
 *
 * @Type\Document("News")
 */
class News extends Article
{
}
