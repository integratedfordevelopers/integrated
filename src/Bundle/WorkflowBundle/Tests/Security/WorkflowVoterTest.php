<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Tests\Security;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Integrated\Bundle\UserBundle\Model\GroupableInterface;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Entity\Definition\Permission;
use Integrated\Bundle\WorkflowBundle\Entity\Definition\State;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;
use Integrated\Common\Security\PermissionInterface;
use Integrated\Common\Security\Permissions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowVoterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ManagerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manager;

    /**
     * @var ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resolver;

    /**
     * @var MetadataFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadata;

    // below are variables used to make singletons for the getters. Setting
    // them to null will allow to create a new version.

    /**
     * @var ObjectRepository[]
     */
    private $repository = [];

    /**
     * @var Definition
     */
    private $workflow = null;

    /**
     * @var State
     */
    private $state = null;

    protected function setUp(): void
    {
        $this->manager = $this->createMock('Doctrine\\Persistence\\ManagerRegistry');
        $this->resolver = $this->createMock('Integrated\\Common\\ContentType\\ResolverInterface');
        $this->metadata = $this->createMock(MetadataFactoryInterface::class);
    }

    protected function setUpMetadata($class, $exists = true)
    {
        $metadata = $this->createMock(MetadataInterface::class);
        $metadata->expects($this->atLeastOnce())
            ->method('hasOption')
            ->with('workflow')
            ->willReturn($exists);

        $this->metadata->expects($this->atLeastOnce())
            ->method('getMetadata')
            ->with($class)
            ->willReturn($metadata);
    }

    protected function setUpResolver($exists = true)
    {
        $type = $this->createMock('Integrated\\Common\\ContentType\\ContentTypeInterface');
        $type->expects($this->atLeastOnce())
            ->method('hasOption')
            ->with('workflow')
            ->willReturn($exists);

        $type->expects($this->any())
            ->method('getPermissions')
            ->willReturn([$this->getPermission('group-no-match', false, false)]);

        if ($exists) {
            $type->expects($this->atLeastOnce())
                ->method('getOption')
                ->with('workflow')
                ->willReturn('this-is-the-workflow-id');
        } else {
            $type->expects($this->never())
                ->method('getOption');
        }

        $this->resolver->expects($this->atLeastOnce())
            ->method('hasType')
            ->with('type')
            ->willReturn(true);

        $this->resolver->expects($this->atLeastOnce())
            ->method('getType')
            ->with('type')
            ->willReturn($type);
    }

    protected function setUpManager()
    {
        if (!\array_key_exists('workflow', $this->repository)) {
            $this->setUpRepositoryWorkflow();
        }

        if (!\array_key_exists('state', $this->repository)) {
            $this->setUpRepositoryState();
        }

        $this->manager->expects(!$this->repository['state'] ? $this->once() : $this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(['Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition'], ['Integrated\\Bundle\\WorkflowBundle\\Entity\\Workflow\\State'])
            ->willReturnOnConsecutiveCalls($this->repository['workflow'], $this->repository['state']);
    }

    protected function setUpRepositoryWorkflow($exists = true)
    {
        $object = ($exists) ? $this->getWorkflow() : null;

        $repository = $this->createMock('Doctrine\\Persistence\\ObjectRepository');
        $repository->expects($this->once())
            ->method($this->anything())
            ->willReturn($object);

        $this->repository['workflow'] = $repository;

        if (!$exists) {
            $this->repository['state'] = null;
        }
    }

    protected function setUpRepositoryState($exists = true)
    {
        $container = null;

        if ($exists) {
            $object = $this->getState();
            $object->expects($this->atLeastOnce())
                ->method('getWorkflow')
                ->willReturn($this->getWorkflow());

            $container = $this->createMock('Integrated\\Bundle\\WorkflowBundle\\Entity\\Workflow\\State');
            $container->expects($this->atLeastOnce())
                ->method('getState')
                ->willReturn($object);
        }

        $repository = $this->createMock('Doctrine\\Persistence\\ObjectRepository');
        $repository->expects($this->once())
            ->method($this->anything())
            ->willReturn($container);

        $this->repository['state'] = $repository;
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Symfony\\Component\\Security\\Core\\Authorization\\Voter\\VoterInterface', $this->getInstance());
    }

    public function testConstructorPermissionsError()
    {
        $this->expectException(\Symfony\Component\OptionsResolver\Exception\ExceptionInterface::class);

        $this->getInstance(['does_not_exist' => 'gives_a_error']);
    }

    public function testSupportsAttribute()
    {
        $voter = $this->getInstance();

        $this->assertTrue($voter->supportsAttribute(Permissions::VIEW));
        $this->assertTrue($voter->supportsAttribute(Permissions::CREATE));
        $this->assertTrue($voter->supportsAttribute(Permissions::EDIT));
        $this->assertTrue($voter->supportsAttribute(Permissions::DELETE));

        $this->assertFalse($voter->supportsAttribute('NOT_SUPPORTED'));
    }

    public function testSupportsAttributeRenamed()
    {
        $voter = $this->getInstance([
            'view' => 'SUPPORTED',
            'create' => 'SUPPORTED',
            'edit' => 'SUPPORTED',
            'delete' => 'SUPPORTED',
        ]);

        $this->assertFalse($voter->supportsAttribute(Permissions::VIEW));
        $this->assertFalse($voter->supportsAttribute(Permissions::CREATE));
        $this->assertFalse($voter->supportsAttribute(Permissions::EDIT));
        $this->assertFalse($voter->supportsAttribute(Permissions::DELETE));

        $this->assertTrue($voter->supportsAttribute('SUPPORTED'));
    }

    public function testSupportsClass()
    {
        $voter = $this->getInstance();

        $class = $this->getMockClass('Integrated\\Bundle\\UserBundle\\Model\\GroupableInterface');
        $object = $this->createMock('Integrated\\Bundle\\UserBundle\\Model\\GroupableInterface');

        $this->assertTrue($voter->supportsClass($class));
        $this->assertTrue($voter->supportsClass($object));
        $this->assertFalse($voter->supportsClass('stdClass'));
        $this->assertFalse($voter->supportsClass(new \stdClass()));
    }

    public function testVoteNoContent()
    {
        $this->manager->expects($this->never())->method($this->anything());
        $this->resolver->expects($this->never())->method($this->anything());
        $this->metadata->expects($this->never())->method($this->anything());

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->getInstance()->vote($this->getToken(), new \stdClass(), []));
    }

    public function testVoteNoWorkflowMetadata()
    {
        $content = $this->createMock('Integrated\\Common\\Content\\ContentInterface');
        $class = \get_class($content);

        $this->setUpMetadata($class, false);

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->getInstance()->vote($this->getToken(), $content, []));
    }

    public function testVoteNoWorkflowContentType()
    {
        $content = $this->createMock('Integrated\\Common\\Content\\ContentInterface');
        $content->expects($this->atLeastOnce())
            ->method('getContentType')
            ->willReturn('type');

        $class = \get_class($content);

        $this->setUpMetadata($class);
        $this->setUpResolver(false);

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->getInstance()->vote($this->getToken(), $content, []));
    }

    public function testVoteNoContentType()
    {
        $content = $this->createMock('Integrated\\Common\\Content\\ContentInterface');
        $content->expects($this->atLeastOnce())
            ->method('getContentType')
            ->willReturn('type');

        $class = \get_class($content);

        $this->setUpMetadata($class);

        $this->resolver->expects($this->atLeastOnce())
            ->method('hasType')
            ->with('type')
            ->willReturn(false);

        $this->resolver->expects($this->never())
            ->method('getType');

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->getInstance()->vote($this->getToken(), $content, []));
    }

    public function testVoteNoWorkflow()
    {
        $content = $this->createMock('Integrated\\Common\\Content\\ContentInterface');
        $content->expects($this->atLeastOnce())
            ->method('getContentType')
            ->willReturn('type');

        $class = \get_class($content);

        $this->setUpMetadata($class);
        $this->setUpResolver();
        $this->setUpRepositoryWorkflow(false);
        $this->setUpManager();

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->getInstance()->vote($this->getToken(), $content, []));
    }

    public function testVoteNoState()
    {
        $content = $this->createMock('Integrated\\Common\\Content\\ContentInterface');
        $content->expects($this->atLeastOnce())
            ->method('getContentType')
            ->willReturn('type');

        $class = \get_class($content);

        $this->setUpMetadata($class);
        $this->setUpResolver();
        $this->setUpRepositoryState(false);
        $this->setUpManager();

        $workflow = $this->getWorkflow();
        $workflow->expects($this->once())
            ->method('getStates')
            ->willReturn([$this->getState()]);

        $voter = $this->getInstance();

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $voter->vote($this->getToken(), $content, []));
        $this->assertSame($this->getState(), $voter->state);
    }

    public function testVoteNoStateWorkflowEmpty()
    {
        $content = $this->createMock('Integrated\\Common\\Content\\ContentInterface');
        $content->expects($this->atLeastOnce())
            ->method('getContentType')
            ->willReturn('type');

        $class = \get_class($content);

        $this->setUpMetadata($class);
        $this->setUpResolver();
        $this->setUpRepositoryState(false);
        $this->setUpManager();

        $workflow = $this->getWorkflow();
        $workflow->expects($this->once())
            ->method('getStates')
            ->willReturn([]);

        $voter = $this->getInstance();

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $voter->vote($this->getToken(), $content, []));
        $this->assertNull($voter->state);
    }

    public function testVoteStateWorkflowNotMatch()
    {
        $content = $this->createMock('Integrated\\Common\\Content\\ContentInterface');
        $content->expects($this->atLeastOnce())
            ->method('getContentType')
            ->willReturn('type');

        $class = \get_class($content);

        $this->setUpMetadata($class);
        $this->setUpResolver();

        $workflow = $this->getWorkflow();
        $workflow->expects($this->once())
            ->method('getStates')
            ->willReturn([$this->getState()]);

        $this->setUpRepositoryWorkflow();
        $this->workflow = null;
        $this->setUpManager();

        $voter = $this->getInstance();

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $voter->vote($this->getToken(), $content, []));
        $this->assertSame($this->getState(), $voter->state);
    }

    /**
     * @dataProvider voteNotSupportedProvider
     */
    public function testVoteNotSupported(TokenInterface $token, array $attributes, $expected)
    {
        $content = $this->createMock('Integrated\\Common\\Content\\ContentInterface');
        $content->expects($this->atLeastOnce())
            ->method('getContentType')
            ->willReturn('type');

        $class = \get_class($content);

        $this->setUpMetadata($class);
        $this->setUpResolver();
        $this->setUpManager();

        $this->assertEquals($expected, $this->getInstance()->vote($token, $content, $attributes));
    }

    public function voteNotSupportedProvider()
    {
        return [
            'class' => [
                $this->getToken(), [], VoterInterface::ACCESS_ABSTAIN,
            ],
            'class but valid attribute' => [
                $this->getToken(), [Permissions::VIEW, Permissions::EDIT], VoterInterface::ACCESS_GRANTED,
            ],
            'class but invalid attribute' => [
                $this->getToken(), ['NOTSUPPORTED'], VoterInterface::ACCESS_ABSTAIN,
            ],
        ];
    }

    /**
     * @dataProvider voteProvider
     */
    public function testVote(array $permissions, array $attributes, $expected)
    {
        $content = $this->createMock('Integrated\\Common\\Content\\ContentInterface');
        $content->expects($this->atLeastOnce())
            ->method('getContentType')
            ->willReturn('type');

        $class = \get_class($content);

        $this->setUpMetadata($class);
        $this->setUpResolver();
        $this->setUpManager();

        $voter = $this->getInstance();
        $voter->permissions = $permissions;

        $this->assertEquals($expected, $voter->vote($this->getToken(), $content, $attributes));
        $this->assertSame($this->getState(), $voter->state);
    }

    public function voteProvider()
    {
        return [
            'view' => [
                ['read' => true, 'write' => false], [Permissions::VIEW], VoterInterface::ACCESS_GRANTED,
            ],
            'view denied' => [
                ['read' => false, 'write' => true], [Permissions::VIEW], VoterInterface::ACCESS_DENIED,
            ],
            'create' => [
                ['read' => false, 'write' => true], [Permissions::CREATE], VoterInterface::ACCESS_GRANTED,
            ],
            'create denied' => [
                ['read' => true, 'write' => false], [Permissions::CREATE], VoterInterface::ACCESS_DENIED,
            ],
            'edit' => [
                ['read' => true, 'write' => true], [Permissions::EDIT], VoterInterface::ACCESS_GRANTED,
            ],
            'edit denied' => [
                ['read' => false, 'write' => true], [Permissions::EDIT], VoterInterface::ACCESS_DENIED,
            ],
            'delete' => [
                ['read' => true, 'write' => true], [Permissions::DELETE], VoterInterface::ACCESS_GRANTED,
            ],
            'delete denied' => [
                ['read' => false, 'write' => true], [Permissions::DELETE], VoterInterface::ACCESS_DENIED,
            ],
            'mixed' => [
                ['read' => true, 'write' => true], [Permissions::VIEW, Permissions::DELETE], VoterInterface::ACCESS_GRANTED,
            ],
            'mixed denied' => [
                ['read' => true, 'write' => false], [Permissions::VIEW, Permissions::EDIT], VoterInterface::ACCESS_DENIED,
            ],
            'mixed not supports attribute' => [
                ['read' => true, 'write' => false], [Permissions::VIEW, 'NOT_SUPPORTED'], VoterInterface::ACCESS_GRANTED,
            ],
        ];
    }

    public function testGetPermissions()
    {
        $user = $this->getUser(['group']);
        $state = $this->getState([
            $this->getPermission('group', true, false),
            $this->getPermission('group-no-match', false, true),
        ]);

        $this->assertEquals(['read' => true, 'write' => false], $this->getInstance()->getPermissions($user, $state->getPermissions()));
    }

    public function testGetPermissions2()
    {
        $user = $this->getUser(['group']);
        $state = $this->getState([
            $this->getPermission('group', false, true),
            $this->getPermission('group-no-match', true, false),
        ]);

        $this->assertEquals(['read' => false, 'write' => true], $this->getInstance()->getPermissions($user, $state->getPermissions()));
    }

    public function testGetPermissions3()
    {
        $user = $this->getUser(['group']);
        $state = $this->getState([
            $this->getPermission('group', false, false),
            $this->getPermission('group-no-match', true, true),
        ]);

        $this->assertEquals(['read' => false, 'write' => false], $this->getInstance()->getPermissions($user, $state->getPermissions()));
    }

    public function testGetPermissionsComplex()
    {
        $user = $this->getUser(['group-1', 'group-2']);
        $state = $this->getState([
            $this->getPermission('group-1', false, false),
            $this->getPermission('group-2', true, false),
            $this->getPermission('group-3', false, true),
            $this->getPermission('group-4', true, true),
        ]);

        $this->assertEquals(['read' => true, 'write' => false], $this->getInstance()->getPermissions($user, $state->getPermissions()));
    }

    public function testGetPermissionsComplex2()
    {
        $user = $this->getUser(['group-1', 'group-3']);
        $state = $this->getState([
            $this->getPermission('group-1', false, false),
            $this->getPermission('group-2', true, false),
            $this->getPermission('group-3', false, true),
            $this->getPermission('group-4', true, true),
        ]);

        $this->assertEquals(['read' => false, 'write' => true], $this->getInstance()->getPermissions($user, $state->getPermissions()));
    }

    public function testGetPermissionsComplex3()
    {
        $user = $this->getUser(['group-2', 'group-3']);
        $state = $this->getState([
            $this->getPermission('group-1', false, false),
            $this->getPermission('group-2', true, false),
            $this->getPermission('group-3', false, true),
            $this->getPermission('group-4', true, true),
        ]);

        $this->assertEquals(['read' => true, 'write' => true], $this->getInstance()->getPermissions($user, $state->getPermissions()));
    }

    public function testGetPermissionsNoGroups()
    {
        $user = $this->getUser();
        $state = $this->getState([
            $this->getPermission('group', true, true),
        ]);

        $this->assertEquals(['read' => true, 'write' => true], $this->getInstance()->getPermissions($user, $state->getPermissions()));
    }

    public function testGetPermissionsNoPermissions()
    {
        $user = $this->getUser(['group-1', 'group-2']);
        $state = $this->getState([]);

        $this->assertEquals(['read' => true, 'write' => true], $this->getInstance()->getPermissions($user, $state->getPermissions()));
    }

    /**
     * @param string[] $permissions
     *
     * @return Mock\WorkflowVoter
     */
    protected function getInstance(array $permissions = [])
    {
        return new Mock\WorkflowVoter($this->manager, $this->resolver, $this->metadata, $permissions);
    }

    /**
     * @param array $groups
     *
     * @return GroupableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getUser(array $groups = [])
    {
        $mock = $this->createMock('Integrated\Bundle\UserBundle\Model\User');

        if ($groups) {
            foreach ($groups as $index => $name) {
                $group = $this->createMock('Integrated\\Bundle\\UserBundle\\Model\\GroupInterface');
                $group->expects($this->atLeastOnce())
                    ->method('getId')
                    ->willReturn($name);

                $groups[$index] = $group;
            }
        }

        $mock->expects($groups ? $this->atLeastOnce() : $this->any())
            ->method('getGroups')
            ->willReturn($groups);

        $mock->expects($this->any())
            ->method('getRoles')
            ->willReturn([]);

        return $mock;
    }

    /**
     * @param mixed $object
     *
     * @return TokenInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getToken($object = null)
    {
        $mock = $this->createMock('Symfony\\Component\\Security\\Core\\Authentication\\Token\\TokenInterface');

        if ($object === null) {
            $object = $this->getUser();
        }

        $mock->expects($this->any())
            ->method('getUser')
            ->willReturn($object);

        return $mock;
    }

    /**
     * @return Definition|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getWorkflow()
    {
        if ($this->workflow === null) {
            $this->workflow = $this->createMock('Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition');
        }

        return $this->workflow;
    }

    /**
     * @param array $permissions
     *
     * @return State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getState(array $permissions = [], $never = false)
    {
        if ($this->state === null) {
            $this->state = $this->createMock('Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition\\State');

            $this->state->expects($never ? $this->never() : ($permissions ? $this->atLeastOnce() : $this->any()))
                ->method('getPermissions')
                ->willReturn($permissions);
        }

        return $this->state;
    }

    /**
     * @param string $group
     * @param bool   $read
     * @param bool   $write
     *
     * @return Permission|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getPermission($group, $read, $write)
    {
        $mock = $this->createMock('Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition\\Permission');
        $mock->expects($this->any())
            ->method('getGroup')
            ->willReturn($group);

        $mask = 0;
        $mask |= $read ? PermissionInterface::READ : 0;
        $mask |= $write ? PermissionInterface::WRITE : 0;

        $mock->expects($this->any())
            ->method('getMask')
            ->willReturn($mask);

        return $mock;
    }
}
