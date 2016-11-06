<?php

namespace AppBundle\Services;

use AppBundle\Entity\User;
use AppBundle\Interfaces\Repository\CategoryRepositoryInterface;

class BookmarkService
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getTree(User $user)
    {
        $root = $user->getRootCategory();
        $tree = $this->categoryRepository->getChildren($root, true);

        return $tree;
    }
}
