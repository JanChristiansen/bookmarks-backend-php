<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategoriesData extends AbstractFixture implements OrderedFixtureInterface
{
    const ROOT_ID = 1;
    const CATEGORY_ROOT = 'category-root';

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
        $this->addReference(self::CATEGORY_ROOT, $root);
        $manager->persist($root);

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

        /** @var Category[] $children121 */
        $children121 = array();
        for ($i = 1; $i <= 2; $i++) {
            $name = 'category-12-11-1' . $i;
            $children121[$i] = new Category();
            $children121[$i]->setName($name);
            $children121[$i]->setParent($children12[1]);
            $manager->persist($children121[$i]);
            $this->addReference($name, $children121[$i]);
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
