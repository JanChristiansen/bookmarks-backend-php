<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Interfaces\Repository\CategoryRepository;
use AppBundle\Services\BookmarkService;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(serializerGroups={"category"})
     *
     * @param Category $category
     * @return Category
     */
    public function getAction(Category $category)
    {
        $this->checkCategoryOwner($category);

        return $category;
    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(statusCode=204)
     */
    public function deleteAction(Category $category)
    {
        $this->checkCategoryOwner($category);
        $this->categoryRepository->delete($category);
    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(serializerGroups={"category"})
     *
     * @param Request $request
     * @return Category
     */
    public function putAction(Request $request)
    {

    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(serializerGroups={"category"})
     *
     * @param Category $category
     * @return Category
     */
    public function patchAction(Category $category)
    {

    }

    private function checkCategoryOwner(Category $category)
    {
    }
}
