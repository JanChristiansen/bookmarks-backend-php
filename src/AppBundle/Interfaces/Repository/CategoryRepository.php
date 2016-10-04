<?php

namespace AppBundle\Interfaces\Repository;

use AppBundle\Entity\Category;

interface CategoryRepository
{
    /**
     * @param int $id
     * @return null|Category
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
