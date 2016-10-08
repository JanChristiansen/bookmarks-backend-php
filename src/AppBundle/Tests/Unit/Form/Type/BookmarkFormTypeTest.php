<?php

namespace AppBundle\Tests\Unit\Form\Type;

use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Form\Type\BookmarkFormType;

class BookmarkFormTypeTest extends AbstractFormTypeTestCase
{
    public function testSubmitValidData()
    {
        $category = new Category();
        $category->setName('super category');
        $this->persist($category);
        $id = $category->getId();

        $formData = array(
            'name' => 'blablabla',
            'category' => $id
        );

        $form = $this->factory->create(BookmarkFormType::class);

        $expectedBookmark = new Bookmark();
        $expectedBookmark->setName('blablabla');
        $expectedBookmark->setCategory($category);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedBookmark, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    public function testGetBlockPrefix()
    {
        $formType = new BookmarkFormType();
        $this->assertEquals('', $formType->getBlockPrefix());
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntities()
    {
        return array_merge(parent::getEntities(), array(
            Category::class,
        ));
    }
}
