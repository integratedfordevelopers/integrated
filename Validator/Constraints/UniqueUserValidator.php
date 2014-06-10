<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Validator\Constraints;

use Integrated\Bundle\UserBundle\Model\UserManagerInterface;
use Integrated\Bundle\UserBundle\Validator\ManagerValidator;

use Symfony\Component\Validator\Constraint;

use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UniqueUserValidator extends ManagerValidator
{
	/**
	 * @inheritdoc
	 */
	public function validate($object, Constraint $constraint)
	{
		if (!$constraint instanceof UniqueUser) {
			throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\\UniqueUser');
		}

		if (!$constraint->manger instanceof UserManagerInterface) {
			throw new UnexpectedTypeException($constraint->manger, 'Integrated\\Bundle\\UserBundle\\Model\\UserManagerInterface');
		}

		parent::validate($object, $constraint);
	}
} 