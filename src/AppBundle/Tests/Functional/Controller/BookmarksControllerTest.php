<?php

namespace AppBundle\Tests\Functional\Controller;

use AppBundle\Controller\BookmarksController;
use AppBundle\DataFixtures\ORM\LoadBookmarksData;
use AppBundle\DataFixtures\ORM\LoadCategoriesData;
use AppBundle\DataFixtures\ORM\LoadFullTreeBookmarksData;
use AppBundle\DataFixtures\ORM\LoadFullTreeCategoriesData;
use AppBundle\DataFixtures\ORM\LoadFullTreeUsersData;
use AppBundle\DataFixtures\ORM\LoadUsersData;
use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use AppBundle\Tests\Functional\WebTestCase;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\HttpFoundation\Response;

class BookmarksControllerTest extends WebTestCase
{
    /**
     * @var ReferenceRepository
     */
    protected $fixtures;

    public function setUp()
    {
        $this->client = static::createClient(array('debug' => false));

        $this->fixtures = $this
            ->loadFixtures(
                array(
                    LoadFullTreeCategoriesData::class,
                    LoadFullTreeBookmarksData::class,
                    LoadFullTreeUsersData::class,
                    LoadCategoriesData::class,
                    LoadUsersData::class,
                    LoadBookmarksData::class,
                )
            )
            ->getReferenceRepository();

        $this->setBasicAuthentication(LoadUsersData::USERNAME, LoadUsersData::PASSWORD);
    }

    public function testServiceDefinition()
    {
        $controller = $this->getContainer()->get('app.controller.bookmarks');
        $this->assertInstanceOf(BookmarksController::class, $controller);
    }

    public function testGetBookmarksAction()
    {
        $this->setBasicAuthentication(LoadFullTreeUsersData::USERNAME_TREE, LoadFullTreeUsersData::PASSWORD_TREE);

        $expectedResponse = '[{"id":12,"name":"category-11","children":[],"bookmarks":[{"id":3,"name":"bookmark-0","url":"http:\/\/category-11.com\/free-snowden\/0","clicks":124,"position":0},{"id":4,"name":"bookmark-1","url":"http:\/\/category-11.com\/free-snowden\/1","clicks":124,"position":1},{"id":5,"name":"bookmark-2","url":"http:\/\/category-11.com\/free-snowden\/2","clicks":124,"position":2},{"id":6,"name":"bookmark-3","url":"http:\/\/category-11.com\/free-snowden\/3","clicks":124,"position":3},{"id":7,"name":"bookmark-4","url":"http:\/\/category-11.com\/free-snowden\/4","clicks":124,"position":4},{"id":8,"name":"bookmark-5","url":"http:\/\/category-11.com\/free-snowden\/5","clicks":124,"position":5},{"id":9,"name":"bookmark-6","url":"http:\/\/category-11.com\/free-snowden\/6","clicks":124,"position":6},{"id":10,"name":"bookmark-7","url":"http:\/\/category-11.com\/free-snowden\/7","clicks":124,"position":7},{"id":11,"name":"bookmark-8","url":"http:\/\/category-11.com\/free-snowden\/8","clicks":124,"position":8},{"id":12,"name":"bookmark-9","url":"http:\/\/category-11.com\/free-snowden\/9","clicks":124,"position":9},{"id":19,"name":"unique","url":"http:\/\/category-11.com\/free-snowden\/6","clicks":43,"position":6}]},{"id":13,"name":"category-12","children":[{"id":16,"name":"category-12-11","children":[],"bookmarks":[]},{"id":17,"name":"category-12-12","children":[],"bookmarks":[]},{"id":18,"name":"category-12-13","children":[],"bookmarks":[]}],"bookmarks":[]},{"id":14,"name":"category-13","children":[{"id":19,"name":"category-13-11","children":[{"id":22,"name":"category-13-11-11","children":[],"bookmarks":[{"id":13,"name":"bookmark-0","url":"http:\/\/category-13-11-11.com\/free-snowden\/0","clicks":3,"position":0},{"id":14,"name":"bookmark-1","url":"http:\/\/category-13-11-11.com\/free-snowden\/1","clicks":3,"position":1},{"id":15,"name":"bookmark-2","url":"http:\/\/category-13-11-11.com\/free-snowden\/2","clicks":3,"position":2},{"id":16,"name":"bookmark-3","url":"http:\/\/category-13-11-11.com\/free-snowden\/3","clicks":3,"position":3},{"id":17,"name":"bookmark-4","url":"http:\/\/category-13-11-11.com\/free-snowden\/4","clicks":3,"position":4},{"id":18,"name":"bookmark-5","url":"http:\/\/category-13-11-11.com\/free-snowden\/5","clicks":3,"position":5}]},{"id":23,"name":"category-13-11-12","children":[],"bookmarks":[]}],"bookmarks":[]},{"id":20,"name":"category-13-12","children":[],"bookmarks":[]},{"id":21,"name":"category-13-13","children":[],"bookmarks":[]}],"bookmarks":[]},{"id":15,"name":"category-14","children":[],"bookmarks":[]}]';
        $response = $this->makeGetRequest('/bookmarks')->client->getResponse();
        $content = $response->getContent();

        $this->assertJsonResponse($response, 200);

        $decodedResponse = json_decode($content, false);
        $this->assertInternalType('array', $decodedResponse);
        $this->assertEquals($expectedResponse, $content);
        $this->assertCount(4, $decodedResponse);
    }

    public function testGetBookmarkAction()
    {
        /** @var Bookmark $bookmark */
        $bookmark = $this->fixtures->getReference(LoadBookmarksData::REFERENCE);

        $response = $this->makeGetRequest('/bookmarks/' . $bookmark->getId())->client->getResponse();
        $content = $response->getContent();

        $this->assertJsonResponse($response, 200);

        $decodedResponse = json_decode($content, false);

        $expected = new \stdClass();
        $expected->category_id = $bookmark->getCategoryId();
        $expected->id = $bookmark->getId();
        $expected->name = 'user-bookmark-1';
        $expected->url = 'http://category-11.com/free-snowden/0';
        $expected->clicks = 124;
        $expected->position = 0;

        $this->assertEquals($expected, $decodedResponse);
    }

    public function testGetBookmarkActionNotFound()
    {
        $response = $this->makeGetRequest('/bookmarks/99999999999')->client->getResponse();
        $this->assertNotFound($response);
    }

    public function testGetBookmarkActionWrongUser()
    {
        /** @var Bookmark $bookmark */
        $bookmark = $this->fixtures->getReference(LoadBookmarksData::REFERENCE_2);
        $response = $this->makeGetRequest('/bookmarks/' . $bookmark->getId())->client->getResponse();
        $this->assertForbidden($response);
    }

    public function testDeleteBookmarkAction()
    {
        /** @var Bookmark $bookmark */
        $bookmark = $this->fixtures->getReference(LoadBookmarksData::REFERENCE);
        $response = $this->makeDeleteRequest('/bookmarks/' . $bookmark->getId())->client->getResponse();

        $this->assertEmpty($response->getContent());
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testDeleteBookmarkActionNotFound()
    {
        $response = $this->makeDeleteRequest('/bookmarks/99999999999')->client->getResponse();
        $this->assertNotFound($response);
    }

    public function testDeleteBookmarkActionForbidden()
    {
        /** @var Bookmark $bookmark */
        $bookmark = $this->fixtures->getReference(LoadBookmarksData::REFERENCE_2);
        $response = $this->makeDeleteRequest('/bookmarks/' . $bookmark->getId())->client->getResponse();
        $this->assertForbidden($response);
    }

    public function testPatchBookmarkAction()
    {
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE);

        $requestParams = ['name' => 'new name', 'category' => $category->getId(), 'url' => 'gopher://old'];

        /** @var Bookmark $bookmark */
        $bookmark = $this->fixtures->getReference(LoadBookmarksData::REFERENCE);
        $response = $this->makePatchRequest(
            '/bookmarks/' . $bookmark->getId(),
            $requestParams,
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded']
        )->client->getResponse();

        $this->assertNoContent($response);

        /** @var Bookmark $patchedBookmark */
        $patchedBookmark = $this->fixtures->getReference(LoadBookmarksData::REFERENCE);
        $this->assertEquals('new name', $patchedBookmark->getName());
        $this->assertEquals($category->getId(), $patchedBookmark->getCategory()->getId());
        $this->assertEquals('gopher://old', $patchedBookmark->getUrl());
    }

    public function testPatchCategoryActionFormNotValid()
    {
        $requestParams = ['name' => 'new name', 'category' => -1];

        /** @var Bookmark $bookmark */
        $bookmark = $this->fixtures->getReference(LoadBookmarksData::REFERENCE);
        $response = $this->makePatchRequest(
            '/bookmarks/' . $bookmark->getId(),
            $requestParams,
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded']
        )->client->getResponse();

        $expectedResponse = '{"code":400,"message":"Validation Failed","errors":{"children":{"name":{},"url":{},"category":{"errors":["This value is not valid."]}}}}';
        $this->assertEquals($expectedResponse, $response->getContent());
        $this->assertStatusCodeInResponse($response, Response::HTTP_BAD_REQUEST);

        $patchedCategory = $this->fixtures->getReference(LoadBookmarksData::REFERENCE);
        $this->assertEquals(LoadBookmarksData::REFERENCE, $patchedCategory->getName());
    }

    public function testPatchBookmarkActionCategoryOwnerNotBookmarkOwner()
    {
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE_2);

        $requestParams = ['name' => 'new name', 'category' => $category->getId()];

        /** @var Bookmark $bookmark */
        $bookmark = $this->fixtures->getReference(LoadBookmarksData::REFERENCE);
        $response = $this->makePatchRequest(
            '/bookmarks/' . $bookmark->getId(),
            $requestParams,
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded']
        )->client->getResponse();

        $expectedResponse = '{"error":{"code":400,"message":"Bad Request"}}';
        $this->assertEquals($expectedResponse, trim($response->getContent()));
        $this->assertStatusCodeInResponse($response, Response::HTTP_BAD_REQUEST);

        $patchedBookmark = $this->fixtures->getReference(LoadBookmarksData::REFERENCE);
        $this->assertEquals(LoadBookmarksData::REFERENCE, $patchedBookmark->getName());
    }

    public function testPatchBookmarkActionNotSubmitted()
    {
        /** @var Bookmark $bookmark */
        $bookmark = $this->fixtures->getReference(LoadBookmarksData::REFERENCE);
        $response = $this->makePatchRequest('/bookmarks/' . $bookmark->getId())->client->getResponse();

        $expectedResponse = '{"error":{"code":400,"message":"Bad Request"}}';
        $this->assertEquals($expectedResponse, trim($response->getContent()));
        $this->assertStatusCodeInResponse($response, Response::HTTP_BAD_REQUEST);

        $patchedBookmark = $this->fixtures->getReference(LoadBookmarksData::REFERENCE);
        $this->assertEquals(LoadBookmarksData::REFERENCE, $patchedBookmark->getName());
    }

    public function testPatchBookmarkActionNotFound()
    {
        $response = $this->makePatchRequest('/bookmarks/99999999999')->client->getResponse();
        $this->assertNotFound($response);
    }

    public function testPatchBookmarkActionForbidden()
    {
        /** @var Bookmark $bookmark */
        $bookmark = $this->fixtures->getReference(LoadBookmarksData::REFERENCE_2);
        $response = $this->makePatchRequest('/bookmarks/' . $bookmark->getId())->client->getResponse();
        $this->assertForbidden($response);
    }


    public function testPostBookmarkAction()
    {
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE);

        $requestParams = ['name' => 'new name', 'url' => 'https://schmaun.de', 'category' => $category->getId()];
        $response = $this->makePostRequest(
            '/bookmarks',
            $requestParams,
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded']
        )->client->getResponse();

        $decodedResponse = json_decode($response->getContent(), false);

        $this->assertInstanceOf('stdClass', $decodedResponse);
        $this->assertEquals('new name', $decodedResponse->name);
        $this->assertGreaterThan(0, $decodedResponse->id);
        $this->assertStatusCodeInResponse($response, Response::HTTP_OK);
    }

    public function testPostBookmarkActionCategoryOwnerNotBookmarkOwner()
    {
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE_2);

        $requestParams = ['name' => 'new name', 'url' => 'https://schmaun.de', 'category' => $category->getId()];
        $response = $this->makePostRequest(
            '/bookmarks',
            $requestParams,
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded']
        )->client->getResponse();

        $expectedResponse = '{"error":{"code":400,"message":"Bad Request"}}';
        $this->assertEquals($expectedResponse, trim($response->getContent()));
        $this->assertStatusCodeInResponse($response, Response::HTTP_BAD_REQUEST);
    }

    public function testPostBookmarkActionFormNotValid()
    {
        $response = $this->makePostRequest(
            '/bookmarks',
            ['name' => ''],
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded',]
        )->client->getResponse();

        $expectedResponse = '{"code":400,"message":"Validation Failed","errors":{"children":{"name":{"errors":["This value should not be blank."]},"url":{"errors":["This value should not be blank."]},"category":{}}}}';
        $this->assertEquals($expectedResponse, $response->getContent());
        $this->assertStatusCodeInResponse($response, Response::HTTP_BAD_REQUEST);
    }

    public function testPostBookmarkActionFormNotSubmitted()
    {
        $response = $this->makePostRequest(
            '/bookmarks',
            [],
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded',]
        )->client->getResponse();

        $expectedResponse = '{"error":{"code":400,"message":"Bad Request"}}';
        $this->assertEquals($expectedResponse, trim($response->getContent()));
        $this->assertStatusCodeInResponse($response, Response::HTTP_BAD_REQUEST);
    }
}
