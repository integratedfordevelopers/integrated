<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Doctrine;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Integrated\Bundle\UserBundle\Model\ScopeInterface;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Bundle\UserBundle\Model\UserManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UserManager implements UserManagerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @param ObjectManager           $om
     * @param                         $class
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(ObjectManager $om, $class, EncoderFactoryInterface $encoderFactory)
    {
        $this->om = $om;
        $this->repository = $this->om->getRepository($class);

        if (!is_subclass_of($this->repository->getClassName(), 'Integrated\\Bundle\\UserBundle\\Model\\UserInterface')) {
            throw new InvalidArgumentException(sprintf('The class "%s" is not subclass of Integrated\\Bundle\\UserBundle\\Model\\UserInterface', $this->repository->getClassName()));
        }

        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->om;
    }

    /**
     * @return ObjectRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $class = $this->getClassName();

        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function persist(UserInterface $user, $flush = true)
    {
        $this->om->persist($user);

        if ($flush) {
            $this->om->flush($user);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(UserInterface $user, $flush = true)
    {
        $this->om->remove($user);

        if ($flush) {
            $this->om->flush($user);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->om->clear($this->repository->getClassName());
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsername($criteria)
    {
        return $this->repository->findOneBy(['username' => $criteria]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByEmail($criteria)
    {
        return $this->repository->findOneBy(['email' => $criteria]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsernameOrEmail($criteria)
    {
        if ($user = $this->findByUsername($criteria)) {
            return $user;
        }

        return $this->findByEmail($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return $this->repository->getClassName();
    }

    /**
     * {@inheritdoc}
     */
    public function findEnabledByUsernameAndScope($username, ?ScopeInterface $scope = null)
    {
        $builder = $this->createQueryBuilder()
            ->select('User')
            ->leftJoin('User.scope', 'Scope')
            ->where('User.username = :username')
            ->andWhere('User.enabled = true')
            ->setParameter('username', $username);

        if ($scope) {
            $builder->andWhere('(User.scope = :scope)');
            $builder->setParameter('scope', (int) $scope->getId());
        } else {
            $builder->andWhere('(Scope.admin = true)');
        }

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilder()
    {
        return $this->repository->createQueryBuilder('User');
    }

    /**
     * @param int    $id
     * @param string $password
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function changePassword(int $id, string $password): bool
    {
        if (!$user = $this->find($id)) {
            return false;
        }

        $salt = base64_encode(random_bytes(72));

        $user->setPassword($this->encoderFactory->getEncoder($user)->encodePassword($password, $salt));
        $user->setSalt($salt);

        $this->persist($user);

        return true;
    }
}
