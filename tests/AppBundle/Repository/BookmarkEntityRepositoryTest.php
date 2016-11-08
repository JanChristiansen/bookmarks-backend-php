<?php

namespace Tests\AppBundle\Repository;

use AppBundle\DataFixtures\ORM\LoadBookmarksData;
use AppBundle\DataFixtures\ORM\LoadCategoriesData;
use AppBundle\DataFixtures\ORM\LoadFullTreeCategoriesData;
use AppBundle\DataFixtures\ORM\LoadUsersData;
use AppBundle\Entity\Bookmark;
use AppBundle\Repository\BookmarkEntityRepository;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Tests\AppBundle\AbstractWebTestCase;

class BookmarkEntityRepositoryTest extends AbstractWebTestCase
{
    /**
     * @var BookmarkEntityRepository
     */
    protected $repository;

    /**
     * @var ReferenceRepository
     */
    private $fixtureRepository;

    public function setUp() {
        $this->fixtureRepository = $this
            ->loadFixtures(array(LoadFullTreeCategoriesData::class, LoadUsersData::class, LoadCategoriesData::class, LoadBookmarksData::class))
            ->getReferenceRepository();;

        $this->repository = $this->getContainer()->get('doctrine')->getRepository(Bookmark::class);
    }

    public function testGet()
    {
        /** @var Bookmark $bookmark */
        $bookmark = $this->fixtureRepository->getReference(LoadBookmarksData::REFERENCE);

        $expected = new Bookmark();
        $expected->setId($bookmark->getId());
        $expected->setName(LoadBookmarksData::REFERENCE);

        $actual = $this->repository->get($bookmark->getId());

        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getName(), $actual->getName());
    }

    public function testDelete()
    {
        /** @var Bookmark $bookmark */
        $bookmark = $this->fixtureRepository->getReference(LoadBookmarksData::REFERENCE);

        $bookmark = $this->repository->get($bookmark->getId());
        $this->repository->delete($bookmark);

        $actualResult = $this->repository->findOneBy(array('name' => 'unique'));
        $this->assertNull($actualResult);
    }

    public function testSave()
    {
        $category = $this->fixtureRepository->getReference(LoadFullTreeCategoriesData::TREE_CATEGORY_ROOT);
        $user = $this->fixtureRepository->getReference(LoadUsersData::REFERENCE);

        $bookmark = new Bookmark();
        $bookmark->setName('neu');
        $bookmark->setUrl('neu');
        $bookmark->setCategory($category);
        $bookmark->setUser($user);
        $this->repository->save($bookmark);

        /** @var Bookmark $saved */
        $saved = $this->repository->get($bookmark->getId());
        $this->assertEquals($saved->getId(), $saved->getId());
        $this->assertEquals($saved->getName(), $saved->getName());
        $this->assertEquals($saved->getUser()->getId(), $saved->getUser()->getId());
    }
}
