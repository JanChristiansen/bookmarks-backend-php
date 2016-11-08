<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\BookmarksController;
use AppBundle\DataFixtures\ORM\LoadBookmarksData;
use AppBundle\DataFixtures\ORM\LoadCategoriesData;
use AppBundle\DataFixtures\ORM\LoadFullTreeBookmarksData;
use AppBundle\DataFixtures\ORM\LoadFullTreeCategoriesData;
use AppBundle\DataFixtures\ORM\LoadFullTreeUsersData;
use AppBundle\DataFixtures\ORM\LoadUsersData;
use AppBundle\Entity\Bookmark;
use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\AbstractWebTestCase;

class BookmarksControllerTest extends AbstractWebTestCase
{
    /**
     * @var ReferenceRepository
     */
    protected $fixtures;

    public function setUp()
    {
        parent::setUp();

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

        $response = $this->makeGetRequest('/bookmarks')->client->getResponse();
        $content = $response->getContent();

        $this->assertJsonResponse($response, 200);

        $decodedResponse = json_decode($content, false);
        $this->assertInternalType('array', $decodedResponse);
        $this->assertJsonStringEqualsJsonFile(__DIR__ . '/ExpectedGetBookmarksActionResponse.json', $content);
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

    public function testPatchBookmarkActionFormNotValid()
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

        $this->assertForbidden($response);
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

        $this->assertForbidden($response);
    }

    public function provideInvalidParametersAndResponse()
    {
        return [
            [['name' => ''], '{"code":400,"message":"Validation Failed","errors":{"children":{"name":{"errors":["This value should not be blank."]},"url":{"errors":["This value should not be blank."]},"category":{"errors":["This value should not be blank."]}}}}'],
            [['name' => 'hallo'], '{"code":400,"message":"Validation Failed","errors":{"children":{"name":{},"url":{"errors":["This value should not be blank."]},"category":{"errors":["This value should not be blank."]}}}}'],
            [['name' => 'hallo', 'url' => 'whatever'], '{"code":400,"message":"Validation Failed","errors":{"children":{"name":{},"url":{},"category":{"errors":["This value should not be blank."]}}}}'],
        ];
    }

    /**
     * @param string $parameter
     * @param string $expectedResponse
     * @dataProvider provideInvalidParametersAndResponse
     */
    public function testPostBookmarkActionFormNotValid($parameter, $expectedResponse)
    {
        $response = $this->makePostRequest(
            '/bookmarks',
            $parameter,
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded',]
        )->client->getResponse();

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
