<?php

namespace AppBundle\Tests\Repository;

use AppBundle\DataFixtures\ORM\LoadUsersData;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Repository\UserEntityRepository;
use AppBundle\Tests\WebTestCase;
use Doctrine\Common\DataFixtures\ReferenceRepository;

class UserEntityRepositoryTest extends WebTestCase
{
    /**
     * @var UserEntityRepository
     */
    protected $repository;

    /**
     * @var ReferenceRepository
     */
    protected $fixtureRepository;

    public function setUp()
    {
        $this->fixtureRepository = $this
            ->loadFixtures(array(LoadUsersData::class))
            ->getReferenceRepository();

        $this->repository = $this->getContainer()->get('doctrine')->getRepository(User::class);
    }

    public function testServiceDefinition()
    {
        $repository = $this->getContainer()->get('app.repository.user');
        $this->assertInstanceOf(UserEntityRepository::class, $repository);
    }

    public function testGet()
    {
        /** @var User $expected */
        /** @var User $actual */
        $expected = $this->fixtureRepository->getReference(LoadUsersData::REFERENCE_2);
        $actual = $this->repository->get($expected->getId());

        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getUsername(), $actual->getUsername());
    }

    public function testSave()
    {
        $rootCategory = new Category();
        $rootCategory->setName('root');

        $expected = new User();
        $expected->setUsername('other Name');
        $expected->setPassword('secret');
        $expected->setRootCategory($rootCategory);
        $this->repository->save($expected);

        /** @var User $saved */
        $saved = $this->repository->get($expected->getId());
        $this->assertEquals($expected->getId(), $saved->getId());
        $this->assertEquals($expected->getUsername(), $saved->getUsername());
        $this->assertEquals($expected->getPassword(), $saved->getPassword());
        $this->assertEquals($expected->getRootCategory(), $saved->getRootCategory());
    }
}
