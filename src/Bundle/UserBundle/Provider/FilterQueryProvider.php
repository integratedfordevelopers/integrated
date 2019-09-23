<?php

namespace Integrated\Bundle\UserBundle\Provider;

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
    public function filterUsersBy($data)
    {
        $groups = isset($data['groups']) ? array_filter($data['groups']) : null;
        $scope = isset($data['scope']) ? array_filter($data['scope']) : null;

        $users = $this->userManager->createQueryBuilder()->select('User');

        if ($groups) {
            $users
                ->leftJoin('User.groups', 'Groups')
                ->where('Groups IN (:groups)')
                ->setParameter('groups', $groups);
        }

        if ($scope) {
            $users
                ->leftJoin('User.scope', 'Scope')
                ->andWhere('Scope IN (:scope)')
                ->setParameter('scope', $scope);
        }

        if ($data['q']) {
            $users
                ->andWhere('User.username = :q')
                ->setParameter('q', $data['q']);
        }

        return $users->getQuery()->getResult();
    }
}
