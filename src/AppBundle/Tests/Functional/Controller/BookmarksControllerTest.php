<?php

namespace AppBundle\Tests\Functional\Controller;

use AppBundle\Controller\BookmarksController;
use AppBundle\DataFixtures\ORM\LoadBookmarksData;
use AppBundle\DataFixtures\ORM\LoadFullTreeBookmarksData;
use AppBundle\DataFixtures\ORM\LoadFullTreeCategoriesData;
use AppBundle\DataFixtures\ORM\LoadFullTreeUsersData;
use AppBundle\Tests\Functional\WebTestCase;

class BookmarksControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->client = static::createClient(array('debug' => false));

        $this->loadFixtures(array(LoadFullTreeCategoriesData::class, LoadFullTreeBookmarksData::class, LoadFullTreeUsersData::class, LoadBookmarksData::class));

        $this->setBasicAuthentication(LoadFullTreeUsersData::USERNAME_TREE, LoadFullTreeUsersData::PASSWORD_TREE);
    }

    public function testServiceDefinition()
    {
        $controller = $this->getContainer()->get('app.controller.bookmarks');
        $this->assertInstanceOf(BookmarksController::class, $controller);
    }

    public function testGetBookmarksAction()
    {
        $expectedResponse = '[{"id":2,"name":"category-11","children":[],"bookmarks":[{"id":1,"name":"bookmark-0","url":"http:\/\/category-11.com\/free-snowden\/0","clicks":124,"position":0},{"id":2,"name":"bookmark-1","url":"http:\/\/category-11.com\/free-snowden\/1","clicks":124,"position":1},{"id":3,"name":"bookmark-2","url":"http:\/\/category-11.com\/free-snowden\/2","clicks":124,"position":2},{"id":4,"name":"bookmark-3","url":"http:\/\/category-11.com\/free-snowden\/3","clicks":124,"position":3},{"id":5,"name":"bookmark-4","url":"http:\/\/category-11.com\/free-snowden\/4","clicks":124,"position":4},{"id":6,"name":"bookmark-5","url":"http:\/\/category-11.com\/free-snowden\/5","clicks":124,"position":5},{"id":7,"name":"bookmark-6","url":"http:\/\/category-11.com\/free-snowden\/6","clicks":124,"position":6},{"id":8,"name":"bookmark-7","url":"http:\/\/category-11.com\/free-snowden\/7","clicks":124,"position":7},{"id":9,"name":"bookmark-8","url":"http:\/\/category-11.com\/free-snowden\/8","clicks":124,"position":8},{"id":10,"name":"bookmark-9","url":"http:\/\/category-11.com\/free-snowden\/9","clicks":124,"position":9},{"id":17,"name":"unique","url":"http:\/\/category-11.com\/free-snowden\/6","clicks":61,"position":6}]},{"id":3,"name":"category-12","children":[{"id":6,"name":"category-12-11","children":[],"bookmarks":[]},{"id":7,"name":"category-12-12","children":[],"bookmarks":[]},{"id":8,"name":"category-12-13","children":[],"bookmarks":[]}],"bookmarks":[]},{"id":4,"name":"category-13","children":[{"id":9,"name":"category-13-11","children":[{"id":12,"name":"category-13-11-11","children":[],"bookmarks":[{"id":11,"name":"bookmark-0","url":"http:\/\/category-13-11-11.com\/free-snowden\/0","clicks":5,"position":0},{"id":12,"name":"bookmark-1","url":"http:\/\/category-13-11-11.com\/free-snowden\/1","clicks":10,"position":1},{"id":13,"name":"bookmark-2","url":"http:\/\/category-13-11-11.com\/free-snowden\/2","clicks":16,"position":2},{"id":14,"name":"bookmark-3","url":"http:\/\/category-13-11-11.com\/free-snowden\/3","clicks":14,"position":3},{"id":15,"name":"bookmark-4","url":"http:\/\/category-13-11-11.com\/free-snowden\/4","clicks":16,"position":4},{"id":16,"name":"bookmark-5","url":"http:\/\/category-13-11-11.com\/free-snowden\/5","clicks":16,"position":5}]},{"id":13,"name":"category-13-11-12","children":[],"bookmarks":[]}],"bookmarks":[]},{"id":10,"name":"category-13-12","children":[],"bookmarks":[]},{"id":11,"name":"category-13-13","children":[],"bookmarks":[]}],"bookmarks":[]},{"id":5,"name":"category-14","children":[],"bookmarks":[]}]';
        $response = $this->makeGetRequest('/bookmarks')->client->getResponse();
        $content = $response->getContent();

        $this->assertJsonResponse($response, 200);

        $decodedResponse = json_decode($content, false);
        $this->assertInternalType('array', $decodedResponse);
        $this->assertCount(4, $decodedResponse);
        $this->assertEquals($expectedResponse, $content);
    }

    public function testGetBookmarkAction()
    {
        $response = $this->makeGetRequest('/bookmarks/1')->client->getResponse();
        $content = $response->getContent();

        $this->assertJsonResponse($response, 200);

        $decodedResponse = json_decode($content, false);

        $expected = new \stdClass();
        $expected->category_id = 2;
        $expected->id = 1;
        $expected->name = 'bookmark-0';
        $expected->url = 'http://category-11.com/free-snowden/0';
        $expected->clicks = 124;
        $expected->position = 0;

        $this->assertEquals($expected, $decodedResponse);
    }

    public function testGetBookmarkActionWrongUser()
    {
        $expected = '{"error":{"code":404,"message":"Not Found"}}';
        $response = $this->makeGetRequest('/bookmarks/' . LoadBookmarksData::ID)->client->getResponse();
        $content = trim($response->getContent());

        $this->assertJsonResponse($response, 404);
        $this->assertEquals($expected, $content);

    }

    public function testDeleteBookmarkAction()
    {
        $response = $this->makeDeleteRequest('/bookmarks/1')->client->getResponse();

        $this->assertEmpty($response->getContent());
        $this->assertEquals(204, $response->getStatusCode());
    }


    public function testDeleteBookmarkActionWrongUser()
    {
        $expected = '{"error":{"code":404,"message":"Not Found"}}';
        $response = $this->makeDeleteRequest('/bookmarks/' . LoadBookmarksData::ID)->client->getResponse();
        $content = trim($response->getContent());

        $this->assertJsonResponse($response, 404);
        $this->assertEquals($expected, $content);

    }
}
