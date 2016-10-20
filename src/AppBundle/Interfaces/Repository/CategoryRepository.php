<?php

namespace AppBundle\Interfaces\Repository;

use AppBundle\Entity\Category;

interface CategoryRepository extends \Gedmo\Tree\RepositoryInterface
{
    /**
     * @param int $id
     * @return null|object|Category
     */
    public function get($id);

    /**
     * @param array $orderBy
     * @return \AppBundle\Entity\Category[]
     */
    public function findAllAsTree($orderBy);

    /**
     * @param Category $category
     */
    public function delete($category);

    /**
     * @param Category $category
     */
    public function save($category);
}
