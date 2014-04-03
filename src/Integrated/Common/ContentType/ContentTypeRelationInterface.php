<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
interface ContentTypeRelationInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return ContentTypeInterface[]
     */
    public function getContentTypes();

    /**
     * @return bool
     */
    public function getMultiple();
}