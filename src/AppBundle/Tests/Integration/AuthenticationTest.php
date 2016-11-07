<?php

namespace AppBundle\Tests;

class AuthenticationTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function provideUrls()
    {
        return [
            ['GET', '/bookmarks/1'],
            ['GET', '/bookmarks'],
            ['DELETE', '/bookmarks/1'],
            ['POST', '/bookmarks'],
            ['PATCH', '/bookmarks/1'],

            ['GET', '/categories/1'],
            ['GET', '/categories'],
            ['DELETE', '/categories/1'],
            ['POST', '/categories'],
            ['PATCH', '/categories/1'],
        ];
    }

    /**
     * @param string $method
     * @param string $url
     * @dataProvider provideUrls
     */
    public function testNotAuthenticated($method, $url)
    {
        $this->setBasicAuthentication(null, null);

        $response = $this->makeRequest($method, $url, [])->client->getResponse();
        $this->assertNotAuthenticated($response);
    }
}
