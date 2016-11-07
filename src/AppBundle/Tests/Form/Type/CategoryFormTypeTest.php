<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Form\Type\CategoryFormType;
use Symfony\Component\Form\Test\TypeTestCase;

class CategoryFormTypeTest extends AbstractFormTypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'name' => 'blablabla',
            'parent' => 123
        );

        $form = $this->factory->create(CategoryFormType::class, null, ['user' => new User()]);

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