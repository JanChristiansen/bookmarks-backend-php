<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Interfaces\Repository\CategoryRepository;
use AppBundle\Services\BookmarkService;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use FOS\RestBundle\Controller\Annotations as Rest;

class CategoriesController extends FOSRestController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var BookmarkService
     */
    private $bookmarkService;

    /**
     * @param CategoryRepository $categoryRepository
     * @param BookmarkService $bookmarkService
     */
    public function __construct(CategoryRepository $categoryRepository, BookmarkService $bookmarkService)
    {
        $this->categoryRepository = $categoryRepository;
        $this->bookmarkService = $bookmarkService;
    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(serializerGroups={"category"})
     *
     * @return Category[]
     */
    public function getCategoriesAction()
    {
        return $this->bookmarkService->getTree($this->getUser());

        /** @var User $user */
        $user = $this->getUser();

        return $this->categoryRepository->childrenHierarchy();
    }
}
