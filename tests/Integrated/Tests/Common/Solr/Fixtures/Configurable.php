<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Solr\Fixtures;

use Integrated\Common\Solr\Configurable as BaseConfigurable;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Configurable extends BaseConfigurable
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'key1' => 'default1',
            'key2' => 'default2'
        ]);
    }
}
