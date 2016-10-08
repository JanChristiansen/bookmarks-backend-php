<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BookmarkCategoryOwnerMatching extends Constraint
{
    public $message = 'Bookmark and category owner not matching.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
