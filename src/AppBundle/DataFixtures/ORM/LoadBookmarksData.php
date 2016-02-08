<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadBookmarksData extends AbstractFixture implements OrderedFixtureInterface
{
    const ID = 1000;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $bookmark = new Bookmark();
        $bookmark->setId(self::ID);
        $bookmark->setName('user-bookmark-1');
        $bookmark->setUser($this->getReference('user'));
        $bookmark->setUrl('adsf');
        $bookmark->setClicks(0);
        $bookmark->setPosition(0);

        $metadata = $manager->getClassMetaData(get_class($bookmark));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());

        $manager->persist($bookmark);
        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 300;
    }
}
