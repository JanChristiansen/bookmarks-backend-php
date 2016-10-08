<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BookmarkCategoryOwnerMatchingValidator extends ConstraintValidator
{
    public function validate($bookmark, Constraint $constraint)
    {
        /** @var $constraint BookmarkCategoryOwnerMatching */
        /** @var $bookmark Bookmark */
        if (!$bookmark->getUser()) {
            // && $bookmark->getCategory() instanceof Category)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('user')
                ->addViolation();
        }

        $category = $bookmark->getCategory();
        if ($category instanceof Category && ($category->getUser() )) {
            // && $bookmark->getCategory() instanceof Category)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('user')
                ->addViolation();
        }
    }
}