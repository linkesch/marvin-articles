<?php

use Marvin\Marvin\Test\FunctionalTestCase;

class FrontendTest extends FunctionalTestCase
{
    public function testArticlesList()
    {
        $client = $this->createClient();
        $this->logIn($client);
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());

        $this->assertCount(1, $crawler->filter('.article'));
    }

    public function testArticleDetail()
    {
        $client = $this->createClient();
        $this->logIn($client);
        $client->request('GET', '/home/article/hello-world');

        $this->assertTrue($client->getResponse()->isOk());
    }
}
