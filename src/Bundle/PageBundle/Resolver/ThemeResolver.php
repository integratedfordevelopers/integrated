<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Resolver;

use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Common\Channel\Connector\Config\ResolverInterface;
use Integrated\Common\Content\Channel\ChannelInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ThemeResolver
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var ThemeManager
     */
    private $themeManager;

    /**
     * @param ResolverInterface $resolver
     * @param ThemeManager      $themeManager
     */
    public function __construct(ResolverInterface $resolver, ThemeManager $themeManager)
    {
        $this->resolver = $resolver;
        $this->themeManager = $themeManager;
    }

    /**
     * @param ChannelInterface $channel
     *
     * @return string
     */
    public function getTheme(ChannelInterface $channel)
    {
        if ($configs = $this->resolver->getConfigs($channel)) {
            foreach ($configs as $config) {
                if ($config->getAdapter() === 'website') {
                    $theme = $config->getOptions()->get('theme');

                    if ($this->themeManager->hasTheme($theme)) {
                        return $theme;
                    }
                }
            }
        }

        return 'default';
    }
}
