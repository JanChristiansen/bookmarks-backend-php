<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadFullTreeUsersData extends AbstractFixture implements OrderedFixtureInterface
{
    const USERNAME_TREE = '3dw4rd';
    const PASSWORD_TREE = 'asyl';

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername(self::USERNAME_TREE);
        $user->setPassword('$2y$12$TUHtsHqqWZccqjBoZFmfQOMlrYE6Dib1Y1sIxD/q8OvsCi/9Ovozy');
        $user->setRootCategory($this->getReference(LoadFullTreeCategoriesData::TREE_CATEGORY_ROOT));
        $this->setReference('user', $user);
        $manager->persist($user);

        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 201;
    }
}
