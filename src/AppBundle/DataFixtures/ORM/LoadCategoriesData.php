<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategoriesData extends AbstractFixture implements OrderedFixtureInterface
{
    const ROOT_ID = 1;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $root = new Category();
        $root->setName('root');
        $root->setId(self::ROOT_ID);
        $manager->persist($root);
        $this->addReference('category-root', $root);

        /** @var Category[] $children1 */
        $children1 = array();
        for ($i = 1; $i <= 2; $i++) {
            $name = 'category-1' . $i;
            $children1[$i] = new Category();
            $children1[$i]->setName($name);
            $children1[$i]->setParent($root);
            $manager->persist($children1[$i]);
            $this->addReference($name, $children1[$i]);
        }

        /** @var Category[] $children12 */
        $children12 = array();
        for ($i = 1; $i <= 2; $i++) {
            $name = 'category-12-1' . $i;
            $children12[$i] = new Category();
            $children12[$i]->setName($name);
            $children12[$i]->setParent($children1[2]);
            $manager->persist($children12[$i]);
            $this->addReference($name, $children12[$i]);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 1;
    }
}
