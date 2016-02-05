<?php

namespace AppBundle\Tests\Unit;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Repository\CategoryEntityRepository;
use AppBundle\Services\BookmarkService;
use AppBundle\Tests\Functional\Repository\CategoryRepositoryTest;

class BookmarkServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetBookmarks()
    {
        $root = new Category();
        $root->setId(1);

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

        $categoryRepository = $this->getMockWithoutInvokingTheOriginalConstructor(CategoryEntityRepository::class);
        $categoryRepository->expects($this->once())->method('find')->with(56)->willReturn($root);
        $categoryRepository->expects($this->once())->method('getChildren')->with($root, true)->willReturn($children);

        $service = new BookmarkService($categoryRepository);
        $actualTree = $service->getTree();

        $this->assertEquals($children, $actualTree);
    }
}
