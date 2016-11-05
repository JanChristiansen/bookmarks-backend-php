<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategoriesData extends AbstractFixture implements OrderedFixtureInterface
{
    const ROOT_ID = 1;
    const CATEGORY_ROOT = 'category-root';
    const CATEGORY_ROOT_2 = 'category-root-user2';
    const REFERENCE = 'category-11';
    const REFERENCE_2 = 'category-user2-11';

    const ORDER = LoadUsersData::ORDER + 1;

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
        $root->setUser($this->getReference(LoadUsersData::REFERENCE));
        $manager->persist($root);

        /** @var Category[] $children1 */
        $children1 = array();
        for ($i = 1; $i <= 2; $i++) {
            $children1[$i] = $this->createChildrenCategory($manager, 'category-1' . $i, $root);
        }

        /** @var Category[] $children12 */
        $children12 = array();
        for ($i = 1; $i <= 2; $i++) {
            $children12[$i] = $this->createChildrenCategory($manager, 'category-12-1' . $i, $children1[2]);
        }

        /** @var Category[] $children121 */
        $children121 = array();
        for ($i = 1; $i <= 2; $i++) {
            $children121[$i] = $this->createChildrenCategory($manager, 'category-12-11-1' . $i, $children12[1]);
        }


        $root2 = new Category();
        $root2->setName('root2');
        $root2->setUser($this->getReference(LoadUsersData::REFERENCE_2));
        $manager->persist($root2);
        $this->addReference(self::CATEGORY_ROOT_2, $root2);

        /** @var Category[] $children1 */
        $children1 = array();
        for ($i = 1; $i <= 2; $i++) {
            $name = 'category-user2-1' . $i;
            $children1[$i] = new Category();
            $children1[$i]->setName($name);
            $children1[$i]->setParent($root2);
            $manager->persist($children1[$i]);
            $this->addReference($name, $children1[$i]);
        }

        $manager->flush();

        /** @var User $user1 */
        $user1 = $this->getReference(LoadUsersData::REFERENCE);
        $user1->setRootCategory($root);

        /** @var User $user1 */
        $user2 = $this->getReference(LoadUsersData::REFERENCE_2);
        $user2->setRootCategory($root2);

        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return self::ORDER;
    }

    /**
     * @param ObjectManager $manager
     * @param $name
     * @param $parent
     * @return Category
     */
    protected function createChildrenCategory(ObjectManager $manager, $name, $parent)
    {
        $children = new Category();
        $children->setName($name);
        $children->setParent($parent);
        $children->setUser($this->getReference(LoadUsersData::REFERENCE));

        $manager->persist($children);
        $this->addReference($name, $children);

        return $children;
    }
}
