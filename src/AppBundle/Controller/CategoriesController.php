<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
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
    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(serializerGroups={"category"})
     *
     * @param Category $category
     * @return Category
     */
    public function getCategoryAction(Category $category)
    {
        $this->checkCategoryOwner($category);

        return $category;
    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(statusCode=204)
     */
    public function deleteCategoryAction(Category $category)
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
    public function putCategoriesAction(Request $request)
    {

    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(serializerGroups={"category"})
     *
     * @param Category $category
     * @return Category
     */
    public function patchCategoryAction(Category $category)
    {

    }

    /**
     * @param Category $category
     */
    private function checkCategoryOwner(Category $category)
    {
        if (!$category->isOwner($this->getUser())) {
            throw $this->createAccessDeniedException();
        }
    }
}
