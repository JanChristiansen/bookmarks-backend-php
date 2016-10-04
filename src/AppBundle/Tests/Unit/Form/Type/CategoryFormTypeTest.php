<?php

namespace AppBundle\Tests\Unit\Form\Type;

use AppBundle\Entity\Category;
use AppBundle\Form\Type\CategoryFormType;
use Symfony\Component\Form\Test\TypeTestCase;

class CategoryFormTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'name' => 'blablabla',
        );

        $form = $this->factory->create(CategoryFormType::class);

        $object = new Category();
        $object->setName('blablabla');

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    public function testGetBlockPrefix()
    {
        $formType = new CategoryFormType();
        $this->assertEquals('', $formType->getBlockPrefix());
    }
}