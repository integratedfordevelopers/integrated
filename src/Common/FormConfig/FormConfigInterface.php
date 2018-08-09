<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\FormConfig;

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\FormConfigFieldInterface;

interface FormConfigInterface
{
    /**
     * Get the unique identiefier.
     *
     * @return string
     */
    public function getId() : string;

    /**
     * Set the name of the document.
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Get the contentType of the document.
     *
     * @return string
     */
    public function getContentType() : string;

    /**
     * Get the subtitle of the document.
     *
     * @return FormConfigFieldInterface[]
     */
    public function getFields();
}
