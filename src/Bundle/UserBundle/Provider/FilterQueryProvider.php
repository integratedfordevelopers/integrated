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

        if (isset($data['q'])) {
            $queryBuilder
                ->andWhere('User.username LIKE :q')
                ->setParameter('q', '%'.$data['q'].'%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function getGroupChoices($data)
    {
        $sql = 'SELECT s.id, s.name, count(g.group_id) as count FROM security_groups s LEFT JOIN security_user_groups g ON s.id = g.group_id WHERE g.user_id IN (:users) GROUP BY g.group_id HAVING count > 0';

        $query = $this->userManager->getObjectManager()->createNativeQuery($sql, $this->getMapping());

        return $this->prepareChoices($query, $data);
    }

    public function getScopeChoices($data)
    {
        $sql = 'SELECT s.id, s.name, count(u.scope) as count FROM security_scopes s LEFT JOIN security_users u ON s.id = u.scope WHERE u.id IN (:users) GROUP BY u.scope HAVING count > 0';

        $query = $this->userManager->getObjectManager()->createNativeQuery($sql, $this->getMapping());

        return $this->prepareChoices($query, $data);
    }

    private function getMapping()
    {
        $mapping = new ResultSetMapping();
        $mapping->addScalarResult('id', 'id');
        $mapping->addScalarResult('count', 'count');
        $mapping->addScalarResult('name', 'name');

        return $mapping;
    }

    private function prepareChoices($query, $data)
    {
        $query->setParameter('users', array_map(function ($data) {
            return $data->getId();
        }, $data));

        $choices = [];
        foreach ($query->getResult() as $result) {
            $choices[sprintf('%s %d', $result['name'], $result['count'])] = $result['id'];
        }

        return $choices;
    }
}
