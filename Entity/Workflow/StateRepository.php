<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Entity\Workflow;

use Doctrine\ORM\EntityRepository;

use Integrated\Bundle\UserBundle\Model\GroupInterface;
use Integrated\Bundle\UserBundle\Model\UserInterface;

use Integrated\Common\Content\ContentInterface;

use Symfony\Component\Security\Core\Util\ClassUtils;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class StateRepository extends EntityRepository
{
	/**
	 * @inheritdoc
	 */
	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
	{
		return parent::findBy($this->convertCriteria($criteria), $orderBy, $limit, $offset);
	}

	/**
	 * @inheritdoc
	 */
	public function findOneBy(array $criteria, array $orderBy = null)
	{
		return parent::findOneBy($this->convertCriteria($criteria), $orderBy);
	}

	protected function convertCriteria(array $criteria)
	{
		if (isset($criteria['content'])) {
			if ($criteria['content'] instanceof ContentInterface) {
				$criteria['content_id'] = $criteria['content']->getId();
				$criteria['content_class'] = ClassUtils::getRealClass($criteria['content']);

				unset($criteria['content']);
			}
		}

		if (isset($criteria['assigned'])) {
			if ($criteria['assigned'] instanceof UserInterface || $criteria['assigned'] instanceof GroupInterface) {
				$criteria['assigned_id'] = $criteria['assigned']->getId();
				$criteria['assigned_class'] = ClassUtils::getRealClass($criteria['assigned']);

				unset($criteria['assigned']);
			}
		}

		return $criteria;
	}
} 