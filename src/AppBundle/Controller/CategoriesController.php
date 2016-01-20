<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use FOS\RestBundle\Controller\Annotations as Rest;

class CategoriesController extends FOSRestController
{
    /**
     * @Nelmio\ApiDoc()
     *
     * @return Category[]
     */
    public function getCategoriesAction()
    {
        return [
            new Category(),
            new Category(),
            new Category(),
            new Category(),
        ];
    }

}
