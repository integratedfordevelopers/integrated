<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\EventListener\Objects;

use Integrated\Bundle\ContentBundle\Event\ContentEvent;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * @author Marijn Otte <marijn@e-active.nl>
 */
class ContentYoutubeListener
{
    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var string
     */
    protected $env;

    /**
     * @param ThemeManager $themeManager
     * @param EngineInterface $templating
     * @param string $env
     */
    public function __construct(
        ThemeManager $themeManager,
        EngineInterface $templating,
        $env
    ) {
        $this->themeManager = $themeManager;
        $this->templating = $templating;
        $this->env = $env;
    }

    /**
     * @param ContentEvent $contentEvent
     *
     * @throws \Exception
     */
    public function process(ContentEvent $contentEvent)
    {
        try {
            $content = preg_replace_callback(
                '/\[object.*?type=\"youtube\".*?id\="(.+?)".*?\]/',
                function ($matches) {
                    dump($matches);
                    return $this->getTemplate($matches[1]);
                },
                $contentEvent->getContent()
            );

            $contentEvent->setContent($content);
        } catch (\Exception $e) {
            if ('prod' !== $this->env) {
                throw $e;
            }
        }
    }

    /**
     * @param string $youtubeId
     *
     * @return null|string
     * @throws \Integrated\Bundle\ThemeBundle\Exception\CircularFallbackException
     */
    protected function getTemplate(string $youtubeId)
    {
        $template = $this->themeManager->locateTemplate('objects/youtube/default.html.twig');

        return $this->templating->renderResponse(
            $template,
            ['youtubeId' => $youtubeId]
        )->getContent();
    }

}
