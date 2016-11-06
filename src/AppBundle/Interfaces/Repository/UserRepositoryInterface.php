<?php

namespace AppBundle\Interfaces\Repository;

use AppBundle\Entity\User;

/**
 * UserEntityRepository
 */
interface UserRepositoryInterface
{
    /**
     * @param int $id
     * @return User|null|object
     */
    public function get($id);

    /**
     * @param User $user
     */
    public function save(User $user);
}
