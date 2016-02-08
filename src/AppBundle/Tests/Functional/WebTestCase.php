<?php

namespace AppBundle\Tests\Functional;

use Liip\FunctionalTestBundle\Test\WebTestCase as LiipWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;

class WebTestCase extends LiipWebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Crawler
     */
    protected $crawler;

    /**
     * @var array
     */
    protected $server = array();

    /**
     * @param string $url
     * @param array $queryParameters
     * @param array $files
     * @param array $server
     * @return WebTestCase
     */
    protected function makeGetRequest($url, array $queryParameters = array(), $files = array(), $server = array())
    {
        return $this->makeRequest('GET', $url, $queryParameters, $files, $server);
    }

    /**
     * @param string $url
     * @param array $queryParameters
     * @param array $files
     * @param array $server
     * @return WebTestCase
     */
    protected function makePatchRequest($url, array $queryParameters = array(), $files = array(), $server = array())
    {
        return $this->makeRequest('PATCH', $url, $queryParameters, $files, $server);
    }

    /**
     * @param string $url
     * @param array $queryParameters
     * @param array $files
     * @param array $server
     * @return WebTestCase
     */
    protected function makePostRequest($url, array $queryParameters = array(), $files = array(), $server = array())
    {
        return $this->makeRequest('POST', $url, $queryParameters, $files, $server);
    }

    /**
     * @param string $url
     * @param array $queryParameters
     * @param array $files
     * @param array $server
     * @return WebTestCase
     */
    protected function makeDeleteRequest($url, array $queryParameters = array(), $files = array(), $server = array())
    {
        return $this->makeRequest('DELETE', $url, $queryParameters, $files, $server);
    }

    /**
     * @param $requestType
     * @param $url
     * @param $queryParameters
     * @param $files
     * @param $server
     * @return WebTestCase
     */
    protected function makeRequest($requestType, $url, $queryParameters, $files = array(), $server = array())
    {
        $server = array_merge($this->server, $server);

        $this->crawler = $this->client->request($requestType, $url, $queryParameters, $files, $server);

        return $this;
    }

    /**
     * @param string $userName
     * @param string $password
     * @return WebTestCase
     */
    protected function setBasicAuthentication($userName, $password)
    {
        $this->server['PHP_AUTH_USER'] = $userName;
        $this->server['PHP_AUTH_PW'] = $password;

        return $this;
    }

    /**
     * @return WebTestCase
     */
    protected function clearBasicAuthentication()
    {
        $this->server['PHP_AUTH_USER'] = null;
        $this->server['PHP_AUTH_PW'] = null;

        return $this;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int $statusCode
     */
    protected function assertJsonResponse($response, $statusCode = 200)
    {
        $this->assertEquals($statusCode, $response->getStatusCode(), $response->getContent());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), $response->headers);
    }
}
