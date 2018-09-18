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

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\Exception\NotFoundException;
use Iterator;

interface FormConfigManagerInterface
{
    /**
     * Get a form configuration by content type and name.
     *
     * @param ContentTypeInterface $type
     * @param string               $key
     *
     * @return FormConfigInterface
     *
     * @throws NotFoundException when the form configuration is not found
     */
    public function get(ContentTypeInterface $type, string $key): FormConfigInterface;

    /**
     * Check if a form configuration exist for the content type and key combination.
     *
     * @param ContentTypeInterface $type
     * @param string               $key
     *
     * @return bool
     */
    public function has(ContentTypeInterface $type, string $key): bool;

    /**
     * Get all the form configurations if a $type is given then only configurations for
     * that content type will be returned.
     *
     * @param ContentTypeInterface $type
     *
     * @return FormConfigInterface[] | Iterator
     */
    public function all(ContentTypeInterface $type = null): Iterator;

    /**
     * Remove the form configuration.
     *
     * @param FormConfigInterface $config
     */
    public function remove(FormConfigInterface $config): void;

    /**
     * Save the form configuration.
     *
     * @param FormConfigInterface $config
     */
    public function save(FormConfigInterface $config): void;
}
