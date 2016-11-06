<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\Type\CategoryFormType;
use AppBundle\Interfaces\Repository\CategoryRepositoryInterface;
use AppBundle\Services\BookmarkService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriesController extends AbstractController
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var BookmarkService
     */
    private $bookmarkService;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param BookmarkService $bookmarkService
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository, BookmarkService $bookmarkService)
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
        $this->checkLoggedInUserIsCategoryOwner($category);

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
        $this->checkLoggedInUserIsCategoryOwner($category);
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
     *    400="Validation error",
     *    403="Access denied"
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
        $form = $this->createForm(CategoryFormType::class, $category, ['user' => $this->getUser()]);
        if ($this->handleForm($form, $request)) {
            $this->checkLoggedInUserIsCategoryOwner($category->getParent());
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
        $this->checkLoggedInUserIsCategoryOwner($category);

        $form = $this->createForm(
            CategoryFormType::class,
            $category,
            ['method' => Request::METHOD_PATCH, 'user' => $this->getUser()]
        );
        if ($this->handleForm($form, $request)) {
            $this->checkLoggedInUserIsCategoryOwner($category->getParent());
            $this->categoryRepository->save($category);

            return $this->view(null, Response::HTTP_NO_CONTENT);
        }

        return View::create($form, Response::HTTP_BAD_REQUEST);
    }

}
