<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Mailer;

use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class FormMailer
{
    /**
     * @var string
     */
    protected $template = 'email/block/form.html.twig';

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var TwigEngine
     */
    protected $twigEngine;

    /**
     * @var ThemeManager
     */
    private $themeManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param \Swift_Mailer           $mailer
     * @param ChannelContextInterface $channelContext
     * @param TwigEngine              $twigEngine
     * @param ThemeManager            $themeManager
     * @param TranslatorInterface     $translator
     * @param string                  $from
     * @param string                  $name
     */
    public function __construct(\Swift_Mailer $mailer, ChannelContextInterface $channelContext, TwigEngine $twigEngine, ThemeManager $themeManager, TranslatorInterface $translator, $from, $name)
    {
        $this->mailer = $mailer;
        $this->twigEngine = $twigEngine;
        $this->channelContext = $channelContext;
        $this->themeManager = $themeManager;
        $this->translator = $translator;
        $this->from = $from;
        $this->name = $name;
    }

    /**
     * @param array       $data
     * @param array       $emailAddresses
     * @param string|null $title
     *
     * @throws \Integrated\Bundle\ThemeBundle\Exception\CircularFallbackException
     * @throws \Twig\Error\Error
     */
    public function send(array $data, array $emailAddresses = [], ?string $title = null)
    {
        if (!\count($emailAddresses)) {
            return;
        }

        $subject = $this->translator->trans('Form submitted');
        if ($channel = $this->channelContext->getChannel()) {
            $subject = '['.$channel->getName().'] '.$subject;
        }
        if ($title) {
            $subject .= ' - '.$title;
        }

        $body = $this->twigEngine->render($this->themeManager->locateTemplate($this->template), ['data' => $data]);

        $message = (new \Swift_Message($subject))
            ->setBcc($emailAddresses)
            ->setFrom($this->from, $this->name)
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
