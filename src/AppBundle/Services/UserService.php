<?php

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Interfaces\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserRepositoryInterface $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param string $username
     * @param string $password
     * @return User
     */
    public function create($username, $password)
    {
        $user = new User();

        $category = new Category();
        $category->setName(Category::DEFAULT_ROOT_NAME);

        $user->setUsername($username);
        $user->setRootCategory($category);
        $category->setUser($user);

        $encodedPassword = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($encodedPassword);

        $this->userRepository->save($user);

        return $user;
    }
}
