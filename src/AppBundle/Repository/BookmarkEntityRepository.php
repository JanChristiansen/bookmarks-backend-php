<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Bookmark;
use AppBundle\Interfaces\Repository\BookmarkRepository;
use Doctrine\ORM\EntityRepository;

class BookmarkEntityRepository extends EntityRepository implements BookmarkRepository
{
    /**
     * @param Bookmark $bookmark
     */
    public function delete(Bookmark $bookmark)
    {
        $this->_em->remove($bookmark);
        $this->_em->flush($bookmark);
    }
}
