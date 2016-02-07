<?php

namespace AppBundle\Repository;

use AppBundle\Interfaces\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;

class UserEntityRepository extends EntityRepository implements UserRepository
{
}
