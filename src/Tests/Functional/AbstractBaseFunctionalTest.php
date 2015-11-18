<?php

namespace AppBundle\Tests\Functional;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractBaseFunctionalTest extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Container
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->container = $this->client->getContainer();

        $this->createSchema();
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    private function createSchema()
    {
        $em = $this->getEntityManager();

        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        if (!empty($metadatas)) {
            $tool = new SchemaTool($em);
            $tool->dropSchema($metadatas);
            $tool->createSchema($metadatas);
        }
    }

    /**
     * @return array
     */
    protected function getJsonResponse()
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }

    protected function assertStatusSuccess()
    {
        $jsonResponse = $this->getJsonResponse();

        $this->assertArrayHasKey('status', $jsonResponse);

        $this->assertSame('success', $jsonResponse['status']);
    }

    protected function assertResponseHttpOk()
    {
        $response = $this->client->getResponse();

        $this->assertSame(
            Response::HTTP_OK,
            $response->getStatusCode(),
            $response->getContent()
        );
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $jsonArrayContent
     * @param array  $parameters
     * @param array  $files
     * @param array  $server
     * @param bool   $changeHistory
     *
     * @return Crawler
     */
    protected function jsonRequest($method, $uri, array $jsonArrayContent = null, array $parameters = [], array $files = array(), array $server = array(), $changeHistory = true)
    {
        $jsonContent = isset($jsonArrayContent) ? json_encode($jsonArrayContent) : null;

        $mergedServer = array_merge(
            $server,
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        return $this->client->request($method, $uri, $parameters, $files, $mergedServer, $jsonContent, $changeHistory);
    }
}