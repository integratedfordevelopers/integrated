<?php

namespace Integrated\Bundle\UserBundle\Validator\Constraints;

use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AuthenticatorValidator extends ConstraintValidator
{
    /**
     * @var GoogleAuthenticatorInterface
     */
    private $authenticator;

    /**
     * @var string
     */
    private $translationDomain;

    public function __construct(GoogleAuthenticatorInterface $authenticator, string $translationDomain)
    {
        $this->authenticator = $authenticator;
        $this->translationDomain = $translationDomain;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Authenticator) {
            throw new UnexpectedTypeException($constraint, Authenticator::class);
        }

        if (!$this->authenticator->checkCode($constraint->user, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain($this->translationDomain)
                ->addViolation();
        }
    }
}
