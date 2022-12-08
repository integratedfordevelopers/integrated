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

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;
use Integrated\Bundle\UserBundle\Model\User;

class Mailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;

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

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator, KeyGenerator $keyGenerator, ?string $from, ?string $name)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->keyGenerator = $keyGenerator;
        $this->from = $from;
        $this->name = $name;
    }

    /**
     * @param User $user
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendPasswordResetMail(User $user): void
    {
        $timestamp = time();
        $key = $this->keyGenerator->generateKey($timestamp, $user);

        $data = [
            'subject' => '[Integrated] '.$this->translator->trans('Password reset'),
            'user' => $user,
            'timestamp' => $timestamp,
            'key' => $key,
        ];

        $message = (new TemplatedEmail())
            ->from(new Address($this->from, $this->name))
            ->to($user->getUserIdentifier())
            ->htmlTemplate('@IntegratedUser/mail/password.reset.html.twig')
            ->subject($data['subject'])
            ->context($data);

        $this->mailer->send($message);
    }
}
