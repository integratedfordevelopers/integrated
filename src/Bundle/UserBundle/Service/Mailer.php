<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\Error;
use Integrated\Bundle\UserBundle\Doctrine\UserManager;
use Integrated\Bundle\UserBundle\Model\User;

class Mailer
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $templating;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var KeyGenerator
     */
    private $keyGenerator;

    /**
     * @var string|null
     */
    private $from;

    /**
     * @var string|null
     */
    private $name;

    /**
     * Password constructor.
     *
     * @param UserManager         $userManager
     * @param MailerInterface     $mailer
     * @param Environment         $templating
     * @param TranslatorInterface $translator
     * @param KeyGenerator        $keyGenerator
     * @param                     $from
     * @param                     $name
     */
    public function __construct(UserManager $userManager, MailerInterface $mailer, Environment $templating, TranslatorInterface $translator, KeyGenerator $keyGenerator, $from, $name)
    {
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->keyGenerator = $keyGenerator;
        $this->from = $from;
        $this->name = $name;
    }

    /**
     * @param User $user
     *
     * @throws Error
     */
    public function sendPasswordResetMail(User $user): void
    {
        $timestamp = time();
        $key = $this->keyGenerator->generateKey($timestamp, $user);
        $template = 'IntegratedUser/mail/password.reset.html.twig';

        $data = [
            'subject' => '[Integrated] '.$this->translator->trans('Password reset'),
            'user' => $user,
            'timestamp' => $timestamp,
            'key' => $key,
        ];

        $message = (new Email())
            ->from(new Address($this->from, $this->name))
            ->to($user->getUsername())
            ->subject($data['subject'])
            ->html($this->templating->render($template, $data));

        $this->mailer->send($message);
    }
}
