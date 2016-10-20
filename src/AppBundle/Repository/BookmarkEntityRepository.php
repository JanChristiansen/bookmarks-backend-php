<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Bookmark;
use AppBundle\Interfaces\Repository\BookmarkRepository;
use Doctrine\ORM\EntityRepository;

class BookmarkEntityRepository extends EntityRepository implements BookmarkRepository
{
    /**
     * @param int $id
     * @return null|object|Bookmark
     */
    public function get($id)
    {
        return $this->find($id);
    }

    /**
     * @param Bookmark $bookmark
     */
    public function delete(Bookmark $bookmark)
    {
        $this->_em->remove($bookmark);
        $this->_em->flush($bookmark);
    }

    /**
     * @param Bookmark $bookmark
     */
    public function save(Bookmark $bookmark)
    {
        $this->_em->persist($bookmark);
        $this->_em->flush($bookmark);
    }
}
