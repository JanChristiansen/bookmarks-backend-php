<?php

namespace AppBundle\Tests\Unit\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Interfaces\Repository\UserRepositoryInterface;
use AppBundle\Services\UserService;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $userRepository;

    /**
     * @var UserService
     */
    private $service;

    /**
     * @var UserPasswordEncoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $passwordEncoder;

    public function setUp()
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $this->service = new UserService($this->userRepository, $this->passwordEncoder);
    }

    public function testCreate()
    {
        $username = 'foo';
        $password = 'bar';
        $encodedPassword = '123';

        $category = new Category();
        $category->setName(Category::DEFAULT_ROOT_NAME);

        $user = new User();

        $this->passwordEncoder
            ->expects($this->once())
            ->method('encodePassword')
            ->with($this->isInstanceOf(User::class), $password)
            ->willReturn($encodedPassword);

        $user->setUsername($username);
        $user->setRootCategory($category);
        $user->setPassword($encodedPassword);
        $category->setUser($user);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $savedUser = $this->service->create($username, $password);

        $this->assertEquals($user, $savedUser);
    }
}
