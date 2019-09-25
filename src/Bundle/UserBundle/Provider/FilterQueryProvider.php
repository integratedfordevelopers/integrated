<?php

namespace Integrated\Bundle\UserBundle\Provider;

use Doctrine\ORM\Query\ResultSetMapping;
use Integrated\Bundle\UserBundle\Doctrine\UserManager;

class FilterQueryProvider
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param array|null $data
     *
     * @return array
     */
    public function getUsers($data)
    {
        if (!$data) {
            return $this->userManager->findAll();
        }

        $queryBuilder = $this->userManager->createQueryBuilder()->select('User');

        if (isset($data['groups'])) {
            $queryBuilder
                ->leftJoin('User.groups', 'Groups')
                ->where('Groups IN (:groups)')
                ->setParameter('groups', array_filter($data['groups']));
        }

        if (isset($data['scope'])) {
            $queryBuilder
                ->leftJoin('User.scope', 'Scope')
                ->andWhere('Scope IN (:scope)')
                ->setParameter('scope', array_filter($data['scope']));
        }

        if ($data['q']) {
            $queryBuilder
                ->andWhere('User.username = :q')
                ->setParameter('q', $data['q']);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function getGroupChoices($data)
    {
        $objectManager = $this->userManager->getObjectManager();

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('name', 'name');

        $sql = 'SELECT name FROM security_groups';

        $query = $objectManager->createNativeQuery($sql, $rsm);
        $query->getResult();
    }

    public function getScopeChoices($data)
    {
    }
}
