<?php

namespace AppBundle\Tests\Unit\Services;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Repository\CategoryEntityRepository;
use AppBundle\Services\BookmarkService;
use AppBundle\Tests\Functional\Repository\CategoryEntityRepositoryTest;

class BookmarkServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetBookmarks()
    {
        $root = new Category();
        $root->setId(1);

        $user = new User();
        $user->setUsername('test');
        $user->setRootCategory($root);

        $firstChild = new Category();
        $firstChild->setId(2);

        $secondChild = new Category();
        $secondChild->setId(3);

        for($i=1;$i <= 3;$i++) {
            $bookmark = new Bookmark();
            $bookmark->setId($i);
            $firstChild->addBookmark($bookmark);
        }

        for($i=4;$i < 7;$i++) {
            $bookmark = new Bookmark();
            $bookmark->setId($i);
            $secondChild->addBookmark($bookmark);
        }

        $children = array($firstChild, $secondChild);

        $categoryRepository = $this->createMock(CategoryEntityRepository::class);
        $categoryRepository->expects($this->once())->method('getChildren')->with($user->getRootCategory(), true)->willReturn($children);

        $service = new BookmarkService($categoryRepository);
        $actualTree = $service->getTree($user);

        $this->assertEquals($children, $actualTree);
    }
}
