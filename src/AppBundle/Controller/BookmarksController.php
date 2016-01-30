<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Interfaces\Repository\BookmarkRepository;
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
     * @param BookmarkRepository $bookmarkRepository
     */
    public function __construct(BookmarkRepository $bookmarkRepository)
    {
        $this->bookmarkRepository = $bookmarkRepository;
    }

    /**
     * @Nelmio\ApiDoc()
     * @Rest\View(serializerGroups={"tree"})
     *
     * @return Bookmark[]
     */
    public function getBookmarksAction()
    {
        $userRepository = $this->get('doctrine')->getRepository(Category::class);
        //$root = null;
        $root = $userRepository->find(56);

        return $this->getDoctrine()->getRepository('AppBundle:Category')->getChildren($root, true);
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
