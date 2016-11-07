<?php

namespace AppBundle\Tests;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testGetterSetter()
    {
        $root = new Category();

        $user = new User();
        $user->setId(1);
        $user->setActive(false);
        $user->setPassword('secret');
        $user->setRootCategory($root);
        $user->setUsername('super');

        $this->assertEquals(1, $user->getId());
        $this->assertEquals(false, $user->isActive());
        $this->assertEquals('secret', $user->getPassword());
        $this->assertEquals($root, $user->getRootCategory());
        $this->assertEquals('super', $user->getUsername());
        $this->assertNull($user->getSalt());
    }

    public function provideUserId()
    {
        return [
            [123, true],
            [2, false],
        ];
    }

    /**
     * @param int $id
     * @param int $expected
     * @dataProvider provideUserId
     */
    public function testIsSame($id, $expected)
    {
        $user = new User();
        $user->setId(123);
        $user2 = new User();
        $user2->setId($id);

        $this->assertEquals($expected, $user->isSame($user2));
    }

    public function testGetRoles()
    {
        $user = new User();
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testSerializeUnserialize()
    {
        $user = new User();
        $user->setId(1);
        $user->setPassword('secret');
        $user->setUsername('uuuuuser');

        $newUser = new User();

        $serialized = $user->serialize();
        $newUser->unserialize($serialized);

        $this->assertEquals($user, $newUser);
    }
}
