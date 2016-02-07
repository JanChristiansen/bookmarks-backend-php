<?php

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use Entity\Repository\CategoryRepository;

class BookmarkService
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * BookmarkService constructor.
     * @param $categoryRepository
     */
    public function __construct($categoryRepository)
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
