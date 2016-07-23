<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUsersData extends AbstractFixture implements OrderedFixtureInterface
{
    const USERNAME = 'citizenfour';
    const PASSWORD = 'asyl';
    const REFERENCE = 'user-1';

    const REFERENCE_2 = 'user-2';

    const ORDER = 1;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername(self::USERNAME);
        $user->setPassword('$2y$12$TUHtsHqqWZccqjBoZFmfQOMlrYE6Dib1Y1sIxD/q8OvsCi/9Ovozy');
        $this->setReference(self::REFERENCE, $user);
        $manager->persist($user);

        $user2 = new User();
        $user2->setUsername('another-user');
        $user2->setPassword('total egal');
        $this->setReference(self::REFERENCE_2, $user2);
        $manager->persist($user2);

        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return self::ORDER;
    }
}
