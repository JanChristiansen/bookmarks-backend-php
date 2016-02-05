<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Interfaces\Repository\BookmarkRepository;
use AppBundle\Services\BookmarkService;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use FOS\RestBundle\Controller\Annotations as Rest;

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
        return $this->bookmarkService->getTree();
    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(serializerGroups={"bookmark"})
     *
     * @return Bookmark
     */
    public function getBookmarkAction(Bookmark $bookmark)
    {
        return $bookmark;
    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(statusCode=204)
     */
    public function deleteBookmarkAction(Bookmark $bookmark)
    {
        $this->bookmarkRepository->delete($bookmark);
    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(serializerGroups={"bookmark"})
     *
     * @return Bookmark
     */
    public function putBookmarkAction(Bookmark $bookmark)
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
}
