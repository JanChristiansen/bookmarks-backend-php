<?php

namespace AppBundle\Services;

use AppBundle\Entity\Category;
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
     * @return array
     */
    public function getTree()
    {
        //$root = null;
        $root = $this->categoryRepository->find(1);
        $root = $this->categoryRepository->find(56);
        $tree = $this->categoryRepository->getChildren($root, true);

        return $tree;
    }
}
