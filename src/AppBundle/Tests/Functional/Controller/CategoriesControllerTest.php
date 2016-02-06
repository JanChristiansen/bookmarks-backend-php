<?php

namespace AppBundle\Tests\Functional\Controller;

use AppBundle\Controller\CategoriesController;
use AppBundle\DataFixtures\ORM\LoadCategoriesData;
use AppBundle\Tests\Functional\WebTestCase;

class CategoriesControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->client = static::createClient(array('debug' => false));

        $this->loadFixtures(array(LoadCategoriesData::class));
    }

    public function testServiceDefinition()
    {
        $controller = $this->getContainer()->get('app.controller.categories');
        $this->assertInstanceOf(CategoriesController::class, $controller);
    }

    public function testGetCategoriesAction()
    {
        $expectedResponse = '[{"id":1,"name":"root","lft":1,"lvl":0,"rgt":10,"root":1,"__children":[{"id":2,"name":"category-11","lft":2,"lvl":1,"rgt":3,"root":1,"__children":[]},{"id":3,"name":"category-12","lft":4,"lvl":1,"rgt":9,"root":1,"__children":[{"id":4,"name":"category-12-11","lft":5,"lvl":2,"rgt":6,"root":1,"__children":[]},{"id":5,"name":"category-12-12","lft":7,"lvl":2,"rgt":8,"root":1,"__children":[]}]}]}]';
        $content = $this->makeGetRequest('/categories')->client->getResponse()->getContent();

        $decodedResponse = json_decode($content, false);
        $this->assertInternalType('array', $decodedResponse);
        $this->assertInternalType('array', $decodedResponse[0]->__children);
        $this->assertCount(1, $decodedResponse);
        $this->assertCount(2, $decodedResponse[0]->__children);
        $this->assertEquals($expectedResponse, $content);
    }
}
