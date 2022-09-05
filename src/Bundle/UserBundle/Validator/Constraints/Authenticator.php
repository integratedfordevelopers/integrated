<?php

namespace Integrated\Bundle\UserBundle\Validator\Constraints;

use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Symfony\Component\Validator\Constraint;

class Authenticator extends Constraint
{
    public $message = 'code_invalid';

    /**
     * @var TwoFactorInterface
     */
    public $user = null;
}
