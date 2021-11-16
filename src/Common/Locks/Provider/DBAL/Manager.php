<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Locks\Provider\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Integrated\Common\Locks\Exception\InvalidArgumentException;
use Integrated\Common\Locks\Exception\UnexpectedTypeException;
use Integrated\Common\Locks\Filter;
use Integrated\Common\Locks\LockInterface;
use Integrated\Common\Locks\ManagerInterface;
use Integrated\Common\Locks\RequestInterface;
use Integrated\Common\Locks\ResourceInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Manager implements ManagerInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var AbstractPlatform
     */
    protected $platform;

    /**
     * @param Connection $connection
     * @param array      $options
     */
    public function __construct(Connection $connection, array $options)
    {
        $this->connection = $connection;
        $this->platform = $connection->getDatabasePlatform();
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function acquire(RequestInterface $request, $timeout = 0)
    {
        if ($owner = $request->getOwner()) {
            if ($owner->getIdentifier() === null) {
                throw new InvalidArgumentException('The owner identifier can not be null');
            }
        }

        // @todo POTENTIAL PROBLEM: this uses current server time which
        // could not be in sync and that could lead to problems if locks
        // are created on more then one server.

        $created = time();
        $timeout = $request->getTimeout();
        $expires = $timeout === null ? null : $created + $timeout;

        // if the insert succeeds then the lock is made else setting the
        // lock failed.

        try {
            // have the server created a uuid for this lock

            $data = $this->connection->fetchOne('SELECT '.$this->platform->getGuidExpression());
            $data = [
                'id' => $data,
                'resource' => Resource::serialize($request->getResource()),
                'resource_owner' => Resource::serialize($owner),
                'created' => $created,
                'expires' => $expires,
                'timeout' => $timeout,
            ];

            $this->connection->insert($this->options['lock_table_name'], $data);
        } catch (\Exception $e) {
            return null; // expected to be a dup key error
        }

        return Lock::factory($data);
    }

    /**
     * {@inheritdoc}
     */
    public function release($lock)
    {
        if ($lock instanceof LockInterface) {
            $lock = $lock->getId();
        } elseif (!\is_string($lock)) {
            throw new UnexpectedTypeException($lock, 'string or Integrated\Common\Locks\LockInterface');
        }

        // maybe check first if the lock exist ?

        try {
            $this->connection->delete($this->options['lock_table_name'], ['id' => $lock]);
        } catch (\Exception $e) {
            // could not be removed ...
        }
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($lock)
    {
        if ($lock instanceof LockInterface) {
            $lock = $lock->getId();
        } elseif (!\is_string($lock)) {
            throw new UnexpectedTypeException($lock, 'string or Integrated\Common\Locks\LockInterface');
        }

        try {
            $this->connection->beginTransaction();

            $builder = $this->connection->createQueryBuilder();
            $builder
                ->select('l.*')
                ->from($this->options['lock_table_name'], 'l')
                ->where('l.id = '.$builder->createPositionalParameter($lock));

            if ($data = $this->connection->fetchAssociative($builder->getSQL().' '.$this->platform->getForUpdateSQL(), array_values($builder->getParameters()))) {
                if ($data['timeout'] !== null) {
                    $data['expires'] = time() + $data['timeout'];

                    $this->connection->update($this->options['lock_table_name'], ['expires' => $data['expires']], ['id' => $data['id']]);
                }
            }

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();

            return null; // probably should raise a error
        }

        if ($data) {
            return Lock::factory($data);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function find($lock)
    {
        if ($lock instanceof LockInterface) {
            $lock = $lock->getId();
        } elseif (!\is_string($lock)) {
            throw new UnexpectedTypeException($lock, 'string or Integrated\Common\Locks\LockInterface');
        }

        try {
            $builder = $this->connection->createQueryBuilder();
            $builder
                ->select('l.*')
                ->from($this->options['lock_table_name'], 'l')
                ->where('l.id = '.$builder->createPositionalParameter($lock));

            if ($data = $this->connection->fetchAssoc($builder->getSQL(), array_values($builder->getParameters()))) {
                return Lock::factory($data);
            }
        } catch (\Exception $e) {
            return null; // probably should raise a error
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->findBy([]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByResource(ResourceInterface $resource)
    {
        $filter = new Filter();
        $filter->resources[] = $resource;

        return $this->findBy($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function findByOwner(ResourceInterface $resource)
    {
        $filter = new Filter();
        $filter->owners[] = $resource;

        return $this->findBy($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy($filters)
    {
        if (!\is_array($filters)) {
            $filters = [$filters];
        }

        // build the query based on the supplied filter or return all the result
        // if no filters are supplied.

        $builder = $this->connection->createQueryBuilder();

        $builder->select('l.*');
        $builder->from($this->options['lock_table_name'], 'l');

        foreach ($filters as $filter) {
            $where = $builder->expr()->andX();

            if (!$filter instanceof Filter) {
                throw new UnexpectedTypeException($filter, 'Integrated\Common\Locks\Filter');
            }

            $resources = \is_array($filter->resources) ? $filter->resources : [$filter->resources];
            $resources = array_filter($resources);

            if (!empty($resources)) {
                $resources = array_map(['Integrated\\Common\\Locks\\Provider\\DBAL\\Resource', 'serialize'], $resources);
                $resources = array_map([$builder->getConnection(), 'quote'], $resources);

                $where->add($builder->expr()->in('l.resource', $resources));
            }

            $owners = \is_array($filter->owners) ? $filter->owners : [$filter->owners];
            $owners = array_filter($owners);

            if (!empty($owners)) {
                $owners = array_map(['Integrated\\Common\\Locks\\Provider\\DBAL\\Resource', 'serialize'], $owners);
                $owners = array_map([$builder->getConnection(), 'quote'], $owners);

                $where->add($builder->expr()->in('l.resource_owner', $owners));
            }

            if ($where->count()) {
                $builder->orWhere($where);
            }
        }

        // yeah query time

        $results = [];

        try {
            foreach ($this->connection->fetchAllAssociative($builder->getSQL(), array_values($builder->getParameters())) as $data) {
                $results[] = Lock::factory($data);
            }
        } catch (\Exception $e) {
            return null; // probably should raise a error
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        try {
            // Truncate can safely be used as ids are uuid what should always be
            // unique so there should be no issues with reusing the same key that
            // auto id could have.

            $this->connection->executeUpdate($this->platform->getTruncateTableSQL($this->options['lock_table_name']));
        } catch (\Exception $e) {
            // probably should raise a error
        }
    }

    /**
     * Remove all the expired lock set by this provider.
     */
    public function clean()
    {
        try {
            $builder = $this->connection->createQueryBuilder();
            $builder
                ->delete($this->options['lock_table_name'])
                ->where('expires IS NOT NULL AND expires < '.$builder->createPositionalParameter(time()));

            $builder->execute();
        } catch (\Exception $e) {
            // probably should raise a error
        }
    }
}
