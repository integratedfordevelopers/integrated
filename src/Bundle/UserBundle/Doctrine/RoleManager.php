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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Integrated\Bundle\UserBundle\Event\ConfigureRolesEvent;
use Integrated\Bundle\UserBundle\Model\RoleInterface;
use Integrated\Bundle\UserBundle\Model\RoleManagerInterface;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RoleManager implements RoleManagerInterface
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var bool
     */
    private $rolesEventFired = false;

    /**
     * RoleManager constructor.
     *
     * @param ObjectManager            $om
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $class
     * @param string[]                 $roles
     */
    public function __construct(ObjectManager $om, EventDispatcherInterface $eventDispatcher, $class, array $roles = [])
    {
        $this->om = $om;
        $this->eventDispatcher = $eventDispatcher;
        $this->repository = $this->om->getRepository($class);
        $this->roles = $roles;

        if (!is_subclass_of($this->repository->getClassName(), RoleInterface::class)) {
            throw new InvalidArgumentException(sprintf(
                'The class "%s" is not subclass of Integrated\\Bundle\\UserBundle\\Model\\RoleInterface',
                $this->repository->getClassName()
            ));
        }
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
    public function create($role)
    {
        $class = $this->getClassName();

        return new $class($role);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(roleInterface $role, $flush = true)
    {
        $this->om->persist($role);

        if ($flush) {
            $this->om->flush($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(roleInterface $role, $flush = true)
    {
        $this->om->remove($role);

        if ($flush) {
            $this->om->flush($role);
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
    public function findByName($criteria)
    {
        return $this->repository->findOneBy(['name' => $criteria]);
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
     * Get all available roles from (DB, roles.xml and from Events).
     *
     * @return array
     */
    public function getRolesFromSources()
    {
        $this->loadRolesFromSources();

        $roles = $this->roles;

        foreach ($this->findAll() as $role) {
            $roles[$role->getRole()] = (string) $role->getLabel();
        }

        return $roles;
    }

    /**
     * Lazy load roles from events.
     */
    protected function loadRolesFromSources()
    {
        if (!$this->rolesEventFired) {
            $roles = $this->eventDispatcher->dispatch(
                ConfigureRolesEvent::CONFIGURE,
                new ConfigureRolesEvent($this->roles)
            )->getRoles();

            $this->roles = [];

            foreach ($roles as $name => $label) {
                if ($label instanceof RoleInterface) {
                    $name = $label->getRole();
                    $label = $label->getLabel();
                }

                $this->roles[$name] = $label;
            }
        }

        $this->rolesEventFired = true;
    }
}
