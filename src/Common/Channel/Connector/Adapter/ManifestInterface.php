<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Adapter;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ManifestInterface
{
    /**
     * Returns the adaptor name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the adaptor display name.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Returns the adaptor description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Returns the adaptor version.
     *
     * @return string
     */
    public function getVersion();
}
