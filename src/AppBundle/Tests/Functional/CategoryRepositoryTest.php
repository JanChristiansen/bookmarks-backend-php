<?php

namespace AppBundle\Tests\Repository;

use AppBundle\Entity\Category;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class CategoryRepositoryTest extends WebTestCase
{
    public function testGet()
    {
        $food = new Category();
        $food->setName('Food');

        $fruits = new Category();
        $fruits->setName('Fruits');
        $fruits->setParent($food);

        $vegetables = new Category();
        $vegetables->setName('Vegetables');
        $vegetables->setParent($food);

        $carrots = new Category();
        $carrots->setName('Carrots');
        $carrots->setParent($vegetables);

        //

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($food);
        $em->persist($fruits);
        $em->persist($vegetables);
        $em->persist($carrots);
        $em->flush();

        $userRepository = $this->getContainer()->get('doctrine')->getRepository(Category::class);

    }
}
