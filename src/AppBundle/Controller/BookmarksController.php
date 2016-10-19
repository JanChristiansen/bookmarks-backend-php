<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Form\Type\BookmarkFormType;
use AppBundle\Interfaces\Repository\BookmarkRepository;
use AppBundle\Services\BookmarkService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BookmarksController extends AbstractController
{
    /**
     * @var BookmarkRepository
     */
    private $bookmarkRepository;

    /**
     * @var BookmarkService
     */
    private $bookmarkService;

    /**
     * @param BookmarkRepository $bookmarkRepository
     * @param BookmarkService $bookmarkService
     */
    public function __construct(BookmarkRepository $bookmarkRepository, BookmarkService $bookmarkService)
    {
        $this->bookmarkRepository = $bookmarkRepository;
        $this->bookmarkService = $bookmarkService;
    }

    /**
     * Gets all bookmarks in the category tree
     *
     * @Nelmio\ApiDoc(
     *  resource=true,
     *  output={
     *    "class"="array<AppBundle\Entity\Category>"
     *  }
     * )
     * @Rest\View(serializerGroups={"tree"})
     *
     * @return Category[]
     */
    public function getBookmarksAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->bookmarkService->getTree($user);
    }

    /**
     * Get a single bookmark
     *
     * @Nelmio\ApiDoc(
     *  output={
     *    "class"="AppBundle\Entity\Bookmark"
     *  }
     * )
     * @Rest\View(serializerGroups={"bookmark"})
     *
     * @param Bookmark $bookmark
     * @return Bookmark
     */
    public function getBookmarkAction(Bookmark $bookmark)
    {
        $this->checkBookmarkOwner($bookmark);

        return $bookmark;
    }

    /**
     * Delete a bookmark
     *
     * @Nelmio\ApiDoc()
     * @Rest\View(statusCode=204)
     */
    public function deleteBookmarkAction(Bookmark $bookmark)
    {
        $this->checkBookmarkOwner($bookmark);
        $this->bookmarkRepository->delete($bookmark);
    }

    /**
     * Create a new bookmark
     *
     * @Nelmio\ApiDoc(
     *  input="AppBundle\Form\Type\BookmarkFormType",
     *  output={
     *    "class"="AppBundle\Entity\Bookmark"
     *  },
     *  statusCodes={
     *    200="Returned when successful",
     *    400="Validation error"
     *  }
     * )
     * @Rest\View(serializerGroups={"bookmark"})
     *
     * @param Request $request
     * @return Bookmark|View
     */
    public function postBookmarkAction(Request $request)
    {
        $bookmark = new Bookmark();
        $form = $this->createForm(BookmarkFormType::class, $bookmark);
        if (!$this->handleForm($form, $request)) {
            return View::create($form, Response::HTTP_BAD_REQUEST);
        }

        $this->checkCategoryAndBookmarkOwner($bookmark);

        $bookmark->setUser($this->getUser());
        $this->bookmarkRepository->save($bookmark);

        return $bookmark;
    }

    /**
     * Change bookmark
     *
     * @Nelmio\ApiDoc(
     *  input="AppBundle\Form\Type\BookmarkFormType",
     *  statusCodes={
     *    200="Returned when successful",
     *    400="Validation error"
     *  }
     * )
     * @Rest\View(serializerGroups={"bookmark"})
     *
     * @param Bookmark $bookmark
     * @param Request $request
     * @return Bookmark|View
     */
    public function patchBookmarkAction(Bookmark $bookmark, Request $request)
    {
        $this->checkBookmarkOwner($bookmark);

        $form = $this->createForm(BookmarkFormType::class, $bookmark, ['method' => Request::METHOD_PATCH]);
        if (!$this->handleForm($form, $request)) {
            return View::create($form, Response::HTTP_BAD_REQUEST);
        }

        $this->checkCategoryAndBookmarkOwner($bookmark);
        $this->bookmarkRepository->save($bookmark);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Bookmark $bookmark
     */
    private function checkBookmarkOwner(Bookmark $bookmark)
    {
        if (!$bookmark->isOwner($this->getUser())) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @param $bookmark
     */
    private function checkCategoryAndBookmarkOwner($bookmark)
    {
        if (!$bookmark->getCategory()->isOwner($this->getUser())) {
            throw new BadRequestHttpException('User mismatch');
        }
    }
}
