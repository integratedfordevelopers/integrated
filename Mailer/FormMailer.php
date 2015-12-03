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

use Symfony\Bridge\Twig\TwigEngine;

use Integrated\Common\Content\ContentInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class FormMailer
{
    /**
     * @var string
     */
    protected $template = 'IntegratedContentBundle:Mailer:form.html.twig';

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var TwigEngine
     */
    protected $twigEngine;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param \Swift_Mailer $mailer
     * @param TwigEngine $twigEngine
     * @param string $from
     * @param string $name
     */
    public function __construct(\Swift_Mailer $mailer, TwigEngine $twigEngine, $from, $name)
    {
        $this->mailer = $mailer;
        $this->twigEngine = $twigEngine;
        $this->from = $from;
        $this->name = $name;
    }

    /**
     * @param array $data
     * @param array $emailAddresses
     */
    public function send(array $data, array $emailAddresses = [])
    {
        if (!count($emailAddresses)) {
            return;
        }

        $message = \Swift_Message::newInstance();
        $message
            ->setBcc($emailAddresses)
            ->setSubject('Form submitted')
            ->setFrom($this->from, $this->name)
            ->setBody($this->twigEngine->render($this->template, ['data' => $data]), 'text/html');

        $this->mailer->send($message);
    }
}
