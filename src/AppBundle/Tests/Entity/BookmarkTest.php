<?php

namespace AppBundle\Tests;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;

class BookmarkTest extends \PHPUnit_Framework_TestCase
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
    public function testIsOwner($owner, $isOwner) {
        $user = new User();
        $user->setId(1);

        $bookmark = new Bookmark();
        $bookmark->setUser($user);
        $this->assertEquals($isOwner, $bookmark->isOwner($owner));
    }

    public function testIsOwnerNoUser()
    {
        $owner = new User();
        $owner->setId(1);

        $bookmark = new Bookmark();

        $this->assertFalse($bookmark->isOwner($owner));
    }

    public function testGetCategoryId_noCategory()
    {
        $bookmark = new Bookmark();
        $this->assertNull($bookmark->getCategoryId());
    }

    public function testGetCategoryId()
    {
        $bookmark = new Bookmark();
        $category = new Category();
        $category->setId(123);
        $bookmark->setCategory($category);
        $this->assertEquals(123, $bookmark->getCategoryId());
    }

    public function testGetterSetter()
    {
        $category = new Category();
        $bookmark = new Bookmark();
        $bookmark->setClicks(123);
        $bookmark->setPosition(3);
        $bookmark->setId(111);
        $bookmark->setUrl('111');
        $bookmark->setCategory($category);

        $this->assertEquals(123, $bookmark->getClicks());
        $this->assertEquals(3, $bookmark->getPosition());
        $this->assertEquals(111, $bookmark->getId());
        $this->assertEquals('111', $bookmark->getUrl());
        $this->assertEquals($category, $bookmark->getCategory());
    }
}
