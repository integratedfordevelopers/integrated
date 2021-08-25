<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Twig;

use Integrated\Bundle\UserBundle\Model\UserInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AuthenticatorExtension extends AbstractExtension
{
    /**
     * @var GoogleAuthenticatorInterface
     */
    private $authenticator;

    public function __construct(GoogleAuthenticatorInterface $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('integrated_user_authenticator_qr_code', [$this, 'getQRCode']),
        ];
    }

    public function getQRCode(UserInterface $user): string
    {
        if ($user->isGoogleAuthenticatorEnabled()) {
            throw new \InvalidArgumentException('Can not generate a QR code for the user when a google authenticator is already enabled.');
        }

        return $this->authenticator->getQRContent($user);
    }
}
