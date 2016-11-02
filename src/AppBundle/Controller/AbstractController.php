<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class AbstractController extends FOSRestController
{
    /**
     * @param Form $form
     * @param Request $request
     * @return bool
     * @throws BadRequestHttpException
     */
    protected function handleForm(Form $form, Request $request)
    {
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            throw new BadRequestHttpException('Not submitted');
        }

        return $form->isValid();
    }

    /**
     * @param Bookmark $bookmark
     */
    protected function checkLoggedInUserIsBookmarkOwner(Bookmark $bookmark)
    {
        if (!$bookmark->isOwner($this->getUser())) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @param Category $category
     */
    protected function checkLoggedInUserIsCategoryOwner(Category $category)
    {
        if (!$category->isOwner($this->getUser())) {
            throw $this->createAccessDeniedException();
        }
    }
}
