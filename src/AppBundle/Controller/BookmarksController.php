<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Form\Type\BookmarkFormType;
use AppBundle\Interfaces\Repository\BookmarkRepositoryInterface;
use AppBundle\Services\BookmarkService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookmarksController extends AbstractController
{
    /**
     * @var BookmarkRepositoryInterface
     */
    private $bookmarkRepository;

    /**
     * @var BookmarkService
     */
    private $bookmarkService;

    /**
     * @param BookmarkRepositoryInterface $bookmarkRepository
     * @param BookmarkService $bookmarkService
     */
    public function __construct(BookmarkRepositoryInterface $bookmarkRepository, BookmarkService $bookmarkService)
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
     *  },
     *  statusCodes={
     *    200="Returned when successful"
     *  }
     * )
     * @Rest\View(serializerGroups={"tree"})
     *
     * @return Category[]
     */
    public function getBookmarksAction()
    {
        return $this->bookmarkService->getTree($this->getUser());
    }

    /**
     * Get a single bookmark
     *
     * @Nelmio\ApiDoc(
     *  output={
     *    "class"="AppBundle\Entity\Bookmark"
     *  },
     *  statusCodes={
     *    200="Returned when successful",
     *    403="Access denied"
     *  }
     * )
     * @Rest\View(serializerGroups={"bookmark"})
     *
     * @param Bookmark $bookmark
     * @return Bookmark
     */
    public function getBookmarkAction(Bookmark $bookmark)
    {
        $this->checkLoggedInUserIsBookmarkOwner($bookmark);

        return $bookmark;
    }

    /**
     * Delete a bookmark
     *
     * @Nelmio\ApiDoc(
     *  statusCodes={
     *    204="Returned when successful",
     *    403="Access denied"
     *  }
     * )
     * @Rest\View(statusCode=204)
     */
    public function deleteBookmarkAction(Bookmark $bookmark)
    {
        $this->checkLoggedInUserIsBookmarkOwner($bookmark);
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
     *    400="Validation error",
     *    403="Access denied"
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

        $this->checkLoggedInUserIsCategoryOwner($bookmark->getCategory());

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
     *    400="Validation error",
     *    403="Access denied"
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
        $this->checkLoggedInUserIsBookmarkOwner($bookmark);

        $form = $this->createForm(BookmarkFormType::class, $bookmark, ['method' => Request::METHOD_PATCH]);
        if (!$this->handleForm($form, $request)) {
            return View::create($form, Response::HTTP_BAD_REQUEST);
        }

        $this->checkLoggedInUserIsCategoryOwner($bookmark->getCategory());
        $this->bookmarkRepository->save($bookmark);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
