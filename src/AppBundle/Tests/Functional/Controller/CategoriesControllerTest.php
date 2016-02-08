<?php

namespace AppBundle\Tests\Functional\Controller;

use AppBundle\Controller\CategoriesController;
use AppBundle\DataFixtures\ORM\LoadCategoriesData;
use AppBundle\DataFixtures\ORM\LoadUsersData;
use AppBundle\Tests\Functional\WebTestCase;

class CategoriesControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->client = static::createClient(array('debug' => false));

        $this->loadFixtures(array(LoadUsersData::class, LoadCategoriesData::class));

        $this->setBasicAuthentication(LoadUsersData::USERNAME, LoadUsersData::PASSWORD);
    }

    public function testServiceDefinition()
    {
        $controller = $this->getContainer()->get('app.controller.categories');
        $this->assertInstanceOf(CategoriesController::class, $controller);
    }

    public function testGetCategoriesAction()
    {
        $expectedResponse = '[{"id":2,"name":"category-11","children":[]},{"id":3,"name":"category-12","children":[{"id":4,"name":"category-12-11","children":[{"id":6,"name":"category-12-11-11","children":[]},{"id":7,"name":"category-12-11-12","children":[]}]},{"id":5,"name":"category-12-12","children":[]}]}]';

        $response = $this->makeGetRequest('/categories')->client->getResponse();
        $content = $response->getContent();

        $this->assertJsonResponse($response, 200);

        $decodedResponse = json_decode($content, false);
        $this->assertInternalType('array', $decodedResponse);
        $this->assertInternalType('array', $decodedResponse[0]->children);
        $this->assertCount(2, $decodedResponse);
        $this->assertCount(0, $decodedResponse[0]->children);
        $this->assertCount(2, $decodedResponse[1]->children);
        $this->assertEquals($expectedResponse, $content);
    }
}
