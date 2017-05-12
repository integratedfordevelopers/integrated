<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk;

use Integrated\Bundle\ContentBundle\Document\Content\Content;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
interface ActionInterface
{
    /**
     * @param Content $content
     * @return mixed
     */
    public function execute(Content $content);

    /**
     * @return string
     */
    public function getTypeOfAction();

    /**
     * @return string
     */
    public function getTargetName();

    /**
     * @return array
     */
    public function getChangeNames();

    /**
     * @return array
     */
    public function getFieldsPreBuildConfig();

    /**
     * @param $data
     * @return mixed
     */
    public function getFieldsPostBuildConfig($data);

}
