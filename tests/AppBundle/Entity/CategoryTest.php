<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

class CategoryTest extends \PHPUnit_Framework_TestCase
{
    public function provideOwner()
    {
        $owner = new User();
        $owner->setId(1);

        $stranger = new User();
        $stranger->setId(2);

        return array(
            array($owner, true),
            array($stranger, false),
        );
    }

    /**
     * @param User $owner
     * @param boolean $isOwner
     * @dataProvider provideOwner
     */
    public function testIsOwner($owner, $isOwner)
    {
        $user = new User();
        $user->setId(1);

        $category = new Category();
        $category->setUser($user);
        $this->assertEquals($isOwner, $category->isOwner($owner));
    }

    public function testIsOwnerNoUser()
    {
        $owner = new User();
        $owner->setId(1);

        $category = new Category();

        $this->assertFalse($category->isOwner($owner));
    }

    public function testBookmarkGetterSetter()
    {
        $category = new Category();

        $bookmark1 = new Bookmark();
        $bookmark1->setId(1);
        $category->addBookmark($bookmark1);

        $bookmark2 = new Bookmark();
        $bookmark2->setId(2);
        $category->addBookmark($bookmark2);

        $this->assertEquals(new ArrayCollection([$bookmark1, $bookmark2]), $category->getBookmarks());

        $bookmarks = new ArrayCollection([$bookmark1, $bookmark2]);
        $bookmarks->removeElement($bookmark1);

        $category->removeBookmark($bookmark1);
        $this->assertEquals($bookmarks, $category->getBookmarks());
    }

    public function testGetterSetter()
    {
        $root = new Category();
        $parent = new Category();
        $children = [new Category(), new Category()];
        $user = new User();

        $category = new Category();
        $category->setUser($user);
        $category->setName('name');
        $category->setId(1);
        $category->setLft(2);
        $category->setLvl(3);
        $category->setRgt(4);
        $category->setRoot($root);
        $category->setParent($parent);
        $category->setChildren($children);

        $this->assertEquals($user, $category->getUser());
        $this->assertEquals('name', $category->getName());
        $this->assertEquals(1, $category->getId());
        $this->assertEquals(2, $category->getLft());
        $this->assertEquals(3, $category->getLvl());
        $this->assertEquals(4, $category->getRgt());
        $this->assertEquals($root, $category->getRoot());
        $this->assertEquals($parent, $category->getParent());
        $this->assertEquals($children, $category->getChildren());
    }
}
