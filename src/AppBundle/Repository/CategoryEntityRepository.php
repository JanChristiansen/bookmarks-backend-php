<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Interfaces\Repository\CategoryRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CategoryEntityRepository extends NestedTreeRepository implements CategoryRepository
{

    /**
     * @param int $id
     * @return null|object|Category
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

    /**
     * @param Category $category
     * @throws Exception
     */
    public function delete($category)
    {
        $this->_em->beginTransaction();
        try {
            $category = $this->find($category->getId(), LockMode::PESSIMISTIC_WRITE);
            $this->_em->remove($category);
            $this->_em->flush();
            $this->_em->commit();
            $this->_em->clear();
        } catch (Exception $e) {
            $this->_em->rollback();
            throw $e;
        }
    }

    /**
     * @param Category $category
     */
    public function save($category)
    {
        $this->_em->persist($category);
        $this->_em->flush($category);
    }

    /**
     * @param User $user
     * @return QueryBuilder
     */
    public function getCategoriesForUserQueryBuilder(User $user)
    {
        return $this->createQueryBuilder('c')
            ->where('c.user = :userId')
            ->orderBy('c.name', 'ASC')
            ->setParameters(['userId' => $user->getId()]);
    }
}
