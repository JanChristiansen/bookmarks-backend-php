<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Interfaces\Repository\CategoryRepository;
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
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(serializerGroups={"tree"})
     *
     * @return Category[]
     */
    public function getCategoriesAction()
    {
        return $this->categoryRepository->childrenHierarchy();
    }
}
