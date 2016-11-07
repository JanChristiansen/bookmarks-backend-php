<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadFullTreeCategoriesData extends AbstractFixture implements OrderedFixtureInterface
{
    const ROOT_ID = 999;
    const TREE_CATEGORY_ROOT = 'tree-category-root';

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
        $this->addReference(self::TREE_CATEGORY_ROOT, $root);

        /** @var Category[] $children1 */
        $children1 = array();
        for ($i = 1; $i <= 4; $i++) {
            $children1[$i] = $this->createChildCategory($manager, 'category-1' . $i, $root);
        }

        /** @var Category[] $children12 */
        $children12 = array();
        for ($i = 1; $i <= 3; $i++) {
            $children12[$i] = $this->createChildCategory($manager, 'category-12-1' . $i, $children1[2]);
        }

        /** @var Category[] $children13 */
        $children13 = array();
        for ($i = 1; $i <= 3; $i++) {
            $children13[$i] = $this->createChildCategory($manager, 'category-13-1' . $i, $children1[3]);
        }

        /** @var Category[] $children131 */
        $children131 = array();
        for ($i = 1; $i <= 2; $i++) {
            $children131[$i] = $this->createChildCategory($manager, 'category-13-11-1' . $i, $children13[1]);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 99;
    }

    /**
     * @param ObjectManager $manager
     * @param $name
     * @param $root
     * @return Category
     */
    protected function createChildCategory(ObjectManager $manager, $name, $root)
    {
        $category = new Category();
        $category->setName($name);
        $category->setParent($root);

        $manager->persist($category);
        $this->addReference('full-tree-' . $name, $category);

        return $category;
    }
}
