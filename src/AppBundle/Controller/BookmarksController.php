<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bookmark;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use FOS\RestBundle\Controller\Annotations as Rest;

class BookmarksController extends FOSRestController
{

    /**
     * @Nelmio\ApiDoc()
     *
     * @return Bookmark[]
     */
    public function getBookmarksAction()
    {
        return [
            new Bookmark(),
            new Bookmark(),
            new Bookmark(),
            new Bookmark(),
        ];
    }

}
