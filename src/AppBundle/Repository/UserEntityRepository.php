<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use AppBundle\Interfaces\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class UserEntityRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * @param int $id
     * @return User|object|null
     */
    public function get($id)
    {
        return $this->find($id);
    }

    /**
     * @param User $user
     */
    public function save(User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush($user);
    }
}
