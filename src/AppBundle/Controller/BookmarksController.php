<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\User;
use AppBundle\Interfaces\Repository\BookmarkRepository;
use AppBundle\Services\BookmarkService;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookmarksController extends FOSRestController
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
     * @Nelmio\ApiDoc()
     * @Rest\View(serializerGroups={"tree"})
     *
     * @return Bookmark[]
     */
    public function getBookmarksAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->bookmarkService->getTree($user);
    }

    /**
     * @Nelmio\ApiDoc()
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
     * @Nelmio\ApiDoc()
     * @Rest\View(statusCode=204)
     */
    public function deleteBookmarkAction(Bookmark $bookmark)
    {
        $this->checkBookmarkOwner($bookmark);
        $this->bookmarkRepository->delete($bookmark);
    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(serializerGroups={"bookmark"})
     *
     * @return Bookmark
     */
    public function putBookmarkAction(Request $request)
    {

    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(serializerGroups={"bookmark"})
     *
     * @return Bookmark
     */
    public function patchBookmarkAction(Bookmark $bookmark)
    {

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
}
