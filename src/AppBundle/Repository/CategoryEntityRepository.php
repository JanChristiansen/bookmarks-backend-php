<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Interfaces\Repository\CategoryRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CategoryEntityRepository extends NestedTreeRepository implements CategoryRepository
{

    /**
     * @param int $id
     * @return null|Category
     */
    public function get($id)
    {
        return $this->find($id);
    }

    /**
     * @param array $orderBy
     * @return \AppBundle\Entity\Category[]
     */
    public function findAllAsTree($orderBy = array('position' => 'ASC'))
    {
        return $this->getChildren();
    }
}
