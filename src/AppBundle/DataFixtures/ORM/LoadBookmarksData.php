<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;


class LoadBookmarksData extends AbstractFixture implements OrderedFixtureInterface
{
    const ID = 1000;
    const REFERENCE = 'user-bookmark-1';
    const REFERENCE_2 = 'user-2-bookmark-2';

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $bookmark = new Bookmark();
        $bookmark->setCategory($this->getReference(LoadCategoriesData::CATEGORY_ROOT));
        $bookmark->setName(self::REFERENCE);
        $bookmark->setUser($this->getReference(LoadUsersData::REFERENCE));
        $bookmark->setUrl('http://category-11.com/free-snowden/0');
        $bookmark->setClicks(124);
        $bookmark->setPosition(0);
        $this->setReference(self::REFERENCE, $bookmark);
        $manager->persist($bookmark);

        $bookmark2 = new Bookmark();
        $bookmark2->setCategory($this->getReference(LoadCategoriesData::CATEGORY_ROOT));
        $bookmark2->setName('the new altavista');
        $bookmark2->setUser($this->getReference(LoadUsersData::REFERENCE_2));
        $bookmark2->setUrl('http://www.google.com/');
        $bookmark2->setClicks(124);
        $bookmark2->setPosition(0);
        $this->setReference(self::REFERENCE_2, $bookmark2);
        $manager->persist($bookmark2);

        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 300;
    }
}
