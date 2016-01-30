<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadFullTreeBookmarksData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $bookmark = new Bookmark();
            $bookmark->setId($i);
            $bookmark->setName('bookmark-' . $i);
            $bookmark->setUrl('http://category-11.com/free-snowden/' . $i);
            $bookmark->setPosition($i);
            $bookmark->setClicks(124);
            $bookmark->setCategory($this->getReference('category-11'));
            $manager->persist($bookmark);
        }

        for ($i = 0; $i < 6; $i++) {
            $bookmark = new Bookmark();
            $bookmark->setName('bookmark-' . $i);
            $bookmark->setUrl('http://category-13-11-11.com/free-snowden/' . $i);
            $bookmark->setPosition($i);
            $bookmark->setClicks(rand(5, 17));
            $bookmark->setCategory($this->getReference('category-13-11-11'));
            $manager->persist($bookmark);
        }

        $bookmark = new Bookmark();
        $bookmark->setId($i);
        $bookmark->setName('unique');
        $bookmark->setUrl('http://category-11.com/free-snowden/' . $i);
        $bookmark->setPosition($i);
        $bookmark->setClicks(rand(0, 213));
        $bookmark->setCategory($this->getReference('category-11'));
        $manager->persist($bookmark);

        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 100;
    }
}
