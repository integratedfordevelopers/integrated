<?php
/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Component\Content;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ContentTypeInterface
{
    /**
     * @return string
     */
    public function getClassName();

    /**
     * @return string
     */
    public function getClassType();

    /**
     * @return array
     */
    public function getFields();

    /**
     * @param $name
     * @return ContentTypeFieldInterface
     */
    public function getField($name);
}