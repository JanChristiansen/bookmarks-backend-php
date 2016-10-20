<?php

namespace AppBundle\Interfaces\Repository;

use AppBundle\Entity\Bookmark;

interface BookmarkRepository
{
    /**
     * @param int $id
     * @return null|object|Bookmark
     */
    public function get($id);

    /**
     * @param Bookmark $bookmark
     */
    public function delete(Bookmark $bookmark);

    /**
     * @param Bookmark $bookmark
     */
    public function save(Bookmark $bookmark);
}
