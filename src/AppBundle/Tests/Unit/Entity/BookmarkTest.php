<?php

namespace AppBundle\Tests\Unit;

use AppBundle\Entity\Bookmark;
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
}
