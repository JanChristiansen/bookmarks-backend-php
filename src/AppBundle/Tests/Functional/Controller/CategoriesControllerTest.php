<?php

namespace AppBundle\Tests\Functional\Controller;

use AppBundle\Controller\CategoriesController;
use AppBundle\DataFixtures\ORM\LoadCategoriesData;
use AppBundle\DataFixtures\ORM\LoadUsersData;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Tests\Functional\WebTestCase;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\HttpFoundation\Response;

class CategoriesControllerTest extends WebTestCase
{
    /**
     * @var ReferenceRepository
     */
    protected $fixtures;

    public function setUp()
    {
        $this->client = static::createClient(array('debug' => false));

        $this->fixtures = $this
            ->loadFixtures(array(LoadUsersData::class, LoadCategoriesData::class))
            ->getReferenceRepository();

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

        $client = $this->makeGetRequest('/categories')->client;
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertStatusCode(Response::HTTP_OK, $client);
        $this->assertJsonResponse($response, Response::HTTP_OK);

        $decodedResponse = json_decode($content, false);
        $this->assertInternalType('array', $decodedResponse);
        $this->assertInternalType('array', $decodedResponse[0]->children);
        $this->assertCount(2, $decodedResponse);
        $this->assertCount(0, $decodedResponse[0]->children);
        $this->assertCount(2, $decodedResponse[1]->children);
        $this->assertEquals($expectedResponse, $content);
    }

    public function testGetCategoryAction()
    {
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE);
        $response = $this->makeGetRequest('/categories/' . $category->getId())->client->getResponse();

        $expectedResponse = '{"id":2,"name":"category-11","children":[]}';
        $decodedResponse = json_decode($expectedResponse, false);

        $this->assertInstanceOf('stdClass', $decodedResponse);
        $this->assertInternalType('array', $decodedResponse->children);
        $this->assertCount(0, $decodedResponse->children);
        $this->assertEquals(2, $decodedResponse->id);
        $this->assertEquals('category-11', $decodedResponse->name);
        $this->assertEquals($expectedResponse, $response->getContent());
        $this->assertStatusCodeInResponse($response, Response::HTTP_OK);
    }

    public function testGetCategoryActionForbidden()
    {
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE_2);
        $response = $this->makeGetRequest('/categories/' . $category->getId())->client->getResponse();
        $this->assertForbidden($response);
    }

    public function testGetCategoryActionNotFound()
    {
        $response = $this->makeGetRequest('/categories/999999999')->client->getResponse();
        $this->assertNotFound($response);
    }

    public function testDeleteCategoryAction()
    {
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE);
        $response = $this->makeDeleteRequest('/categories/' . $category->getId())->client->getResponse();

        $this->assertNoContent($response);
    }

    public function testDeleteCategoryActionForbidden()
    {
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE_2);
        $response = $this->makeDeleteRequest('/categories/' . $category->getId())->client->getResponse();
        $this->assertForbidden($response);
    }

    public function testDeleteCategoryActionNotFound()
    {
        $response = $this->makeDeleteRequest('/categories/999999999')->client->getResponse();
        $this->assertNotFound($response);
    }

    public function testPatchCategoryAction()
    {
        $requestParams = ['name' => 'new name'];
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE);
        $response = $this->makePatchRequest(
            '/categories/' . $category->getId(),
            $requestParams,
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded']
        )->client->getResponse();

        $this->assertNoContent($response);

        $patchedCategory = $this->fixtures->getReference(LoadCategoriesData::REFERENCE);
        $this->assertEquals('new name', $patchedCategory->getName());
    }

    public function testPatchCategoryActionFormNotValid()
    {
        $requestParams = ['name' => '', 'parent' => 123];
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE);
        $response = $this->makePatchRequest(
            '/categories/' . $category->getId(),
            $requestParams,
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded',]
        )->client->getResponse();

        $expectedResponse = '{"code":400,"message":"Validation Failed","errors":{"children":{"name":{"errors":["This value should not be blank."]},"parent":{"errors":["This value is not valid."]}}}}';
        $this->assertEquals($expectedResponse, $response->getContent());
        $this->assertStatusCodeInResponse($response, Response::HTTP_BAD_REQUEST);

        $patchedCategory = $this->fixtures->getReference(LoadCategoriesData::REFERENCE);
        $this->assertEquals('category-11', $patchedCategory->getName());
    }

    public function testPatchCategoryActionFormNotSubmitted()
    {
        $requestParams = [];
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE);
        $response = $this->makePatchRequest(
            '/categories/' . $category->getId(),
            $requestParams,
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded',]
        )->client->getResponse();

        $expectedResponse = '{"error":{"code":400,"message":"Bad Request"}}';
        $this->assertEquals($expectedResponse, trim($response->getContent()));
        $this->assertStatusCodeInResponse($response, Response::HTTP_BAD_REQUEST);

        $patchedCategory = $this->fixtures->getReference(LoadCategoriesData::REFERENCE);
        $this->assertEquals('category-11', $patchedCategory->getName());
    }

    public function testPatchCategoryActionForbidden()
    {
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE_2);
        $response = $this->makePatchRequest('/categories/' . $category->getId())->client->getResponse();
        $this->assertForbidden($response);
    }

    public function testPatchCategoryActionNotFound()
    {
        $response = $this->makePatchRequest('/categories/999999999')->client->getResponse();
        $this->assertNotFound($response);
    }

    public function testPatchCategoryActionParentOwnerNotCategoryOwner()
    {
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE_2);

        $requestParams = ['name' => 'new name', 'parent' => $category->getId()];
        $response = $this->makePostRequest(
            '/categories',
            $requestParams,
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded']
        )->client->getResponse();

        $expectedResponse = '{"code":400,"message":"Validation Failed","errors":{"children":{"name":{},"parent":{"errors":["This value is not valid."]}}}}';
        $this->assertEquals($expectedResponse, $response->getContent());
        $this->assertStatusCodeInResponse($response, Response::HTTP_BAD_REQUEST);

        $patchedCategory = $this->fixtures->getReference(LoadCategoriesData::REFERENCE);
        $this->assertEquals('category-11', $patchedCategory->getName());
    }

    public function testPostCategoryAction()
    {
        $requestParams = ['name' => 'new name', 'parent' => LoadCategoriesData::ROOT_ID];
        $response = $this->makePostRequest(
            '/categories',
            $requestParams,
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded']
        )->client->getResponse();

        $decodedResponse = json_decode($response->getContent(), false);

        $this->assertStatusCodeInResponse($response, Response::HTTP_OK);
        $this->assertInstanceOf('stdClass', $decodedResponse);
        $this->assertInternalType('array', $decodedResponse->children);
        $this->assertCount(0, $decodedResponse->children);
        $this->assertEquals('new name', $decodedResponse->name);
        $this->assertGreaterThan(0, $decodedResponse->id);
    }

    public function testPostCategoryActionFormNotValid()
    {
        $response = $this->makePostRequest(
            '/categories',
            ['name' => ''],
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded',]
        )->client->getResponse();

        $expectedResponse = '{"code":400,"message":"Validation Failed","errors":{"children":{"name":{"errors":["This value should not be blank."]},"parent":{"errors":["This value should not be blank."]}}}}';
        $this->assertEquals($expectedResponse, $response->getContent());
        $this->assertStatusCodeInResponse($response, Response::HTTP_BAD_REQUEST);
    }

    public function testPostCategoryActionFormNotSubmitted()
    {
        $response = $this->makePostRequest(
            '/categories',
            [],
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded',]
        )->client->getResponse();

        $expectedResponse = '{"error":{"code":400,"message":"Bad Request"}}';
        $this->assertEquals($expectedResponse, trim($response->getContent()));
        $this->assertStatusCodeInResponse($response, Response::HTTP_BAD_REQUEST);
    }

    public function testPostCategoryActionParentOwnerNotCategoryOwner()
    {
        /** @var Category $category */
        $category = $this->fixtures->getReference(LoadCategoriesData::REFERENCE_2);

        $response = $this->makePostRequest(
            '/categories',
            ['name' => 'new name', 'parent' => $category->getId()],
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded',]
        )->client->getResponse();

        $expectedResponse = '{"code":400,"message":"Validation Failed","errors":{"children":{"name":{},"parent":{"errors":["This value is not valid."]}}}}';
        $this->assertEquals($expectedResponse, $response->getContent());
        $this->assertStatusCodeInResponse($response, Response::HTTP_BAD_REQUEST);
    }
}
