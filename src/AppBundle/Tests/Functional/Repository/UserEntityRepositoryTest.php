<?php

namespace AppBundle\Tests\Functional\Repository;

use AppBundle\Repository\UserEntityRepository;
use AppBundle\Tests\Functional\WebTestCase;

class UserEntityRepositoryTest extends WebTestCase
{
    public function testServiceDefinition()
    {
        $repository = $this->getContainer()->get('app.repository.user');
        $this->assertInstanceOf(UserEntityRepository::class, $repository);
    }
}
