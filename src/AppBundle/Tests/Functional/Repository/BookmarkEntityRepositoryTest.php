<?php

namespace AppBundle\Tests\Functional\Repository;

use AppBundle\DataFixtures\ORM\LoadFullTreeBookmarksData;
use AppBundle\DataFixtures\ORM\LoadFullTreeCategoriesData;
use AppBundle\Entity\Bookmark;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class BookmarkEntityRepositoryTest extends WebTestCase
{
    public function setUp() {
        $this->loadFixtures(array(LoadFullTreeCategoriesData::class, LoadFullTreeBookmarksData::class));
    }

    public function testDelete()
    {
        $repo = $this->getContainer()->get('doctrine')->getRepository(Bookmark::class);

        $bookmark = $repo->findOneBy(array('name' => 'unique'));
        $repo->delete($bookmark);

        $actualResult = $repo->findOneBy(array('name' => 'unique'));
        $this->assertNull($actualResult);
    }
}
