<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\AssetBundle\Twig\Extension;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class StylesheetExtension extends AbstractAssetExtension
{
    /**
     * @return string
     */
    protected function getTemplate()
    {
        return '@IntegratedAssetBundle/Resources/views/Asset/stylesheets.html.twig';
    }

    /**
     * @return string
     */
    protected function getTag()
    {
        return 'integrated_stylesheets';
    }
}
