<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\Type\CategoryFormType;
use AppBundle\Interfaces\Repository\CategoryRepository;
use AppBundle\Services\BookmarkService;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CategoriesController extends AbstractController
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
     * Gets all categories without the bookmarks
     *
     * @Nelmio\ApiDoc(
     *  resource=true,
     *  output={
     *    "class"="array<AppBundle\Entity\Category>"
     *  },
     *  statusCodes={
     *    200="Returned when successful"
     *  }
     * )
     *
     * @Rest\View(serializerGroups={"category"})
     *
     * @return Category[]
     */
    public function getCategoriesAction()
    {
        return $this->bookmarkService->getTree($this->getUser());
    }

    /**
     * Get a single category
     *
     * @Nelmio\ApiDoc(
     *  output={
     *    "class"="AppBundle\Entity\Category"
     *  },
     *  statusCodes={
     *    200="Returned when successful",
     *    403="Access denied"
     *  }
     * )
     *
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
     * Delete a category
     *
     * @Nelmio\ApiDoc(
     *  statusCodes={
     *    204="Returned when successful",
     *    403="Access denied"
     *  }
     * )
     * @Rest\View(statusCode=204)
     */
    public function deleteCategoryAction(Category $category)
    {
        $this->checkCategoryOwner($category);
        $this->categoryRepository->delete($category);
    }

    /**
     * Create a new category
     *
     * @Nelmio\ApiDoc(
     *  input="AppBundle\Form\Type\CategoryFormType",
     *  output={
     *    "class"="AppBundle\Entity\Category"
     *  },
     *  statusCodes={
     *    200="Returned when successful",
     *    400="Validation error"
     *  }
     * )
     *
     * @Rest\View(serializerGroups={"category"})
     *
     * @param Request $request
     * @return View|Category
     */
    public function postCategoriesAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        if ($this->handleForm($form, $request)) {
            $category->setUser($this->getUser());
            $this->categoryRepository->save($category);

            return $category;
        }

        return View::create($form, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Change the category
     *
     * @Nelmio\ApiDoc(
     *  input="AppBundle\Form\Type\CategoryFormType",
     *  statusCodes={
     *    200="Returned when successful",
     *    400="Validation error",
     *    403="Access denied"
     *  }
     * )
     * @Rest\View(statusCode=204, serializerGroups={"category"})
     *
     * @param Category $category
     * @param Request $request
     * @return View|void
     */
    public function patchCategoryAction(Category $category, Request $request)
    {
        $this->checkCategoryOwner($category);

        $form = $this->createForm(CategoryFormType::class, $category, ['method' => Request::METHOD_PATCH]);
        if ($this->handleForm($form, $request)) {
            $this->categoryRepository->save($category);

            return $this->view(null, Response::HTTP_NO_CONTENT);
        }

        return View::create($form, Response::HTTP_BAD_REQUEST);
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
