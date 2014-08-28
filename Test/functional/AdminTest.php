<?php

use Marvin\Marvin\Test\FunctionalTestCase;

class AdminTest extends FunctionalTestCase
{
    public function testArticlesList()
    {
        $client = $this->createClient();
        $this->logIn($client);
        $client->request('GET', '/admin/articles');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testNewArticle()
    {
        $client = $this->createClient();
        $this->logIn($client);
        $crawler = $client->request('GET', '/admin/articles/form');

        $this->assertTrue($client->getResponse()->isOk());

        $form = $crawler->selectButton('save')->form();
        $crawler = $client->submit($form, array(
            'form[name]' => 'Test article',
        ));

        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/admin/articles');
        $this->assertCount(2, $crawler->filter('#articles tbody tr'));
        $this->assertEquals('Test article', $crawler->filter('table#articles tbody tr:first-child td:first-child')->text());
    }

    public function testEditArticleWithExistingSlug()
    {
        $this->app['db']->executeUpdate("INSERT INTO article (page_id, name, slug, content, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)", array(
            1,
            "Test article",
            "test-article",
            "",
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
        ));

        $client = $this->createClient();
        $this->logIn($client);
        $crawler = $client->request('GET', '/admin/articles/form');

        $this->assertTrue($client->getResponse()->isOk());

        $form = $crawler->selectButton('save')->form();
        $crawler = $client->submit($form, array(
            'form[name]' => 'Test article',
        ));

        $this->assertTrue($client->getResponse()->isOk());

        $data = $this->app['db']->fetchAssoc("SELECT * FROM article WHERE id = 3");
        $this->assertEquals('test-article-2', $data['slug']);
    }

    public function testEditArticle()
    {
        $this->app['db']->executeUpdate("INSERT INTO article (page_id, name, slug, content, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)", array(
            1,
            "Test article",
            "test-article",
            "",
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
        ));

        $client = $this->createClient();
        $this->logIn($client);
        $crawler = $client->request('GET', '/admin/articles/form/2');

        $this->assertTrue($client->getResponse()->isOk());

        $form = $crawler->selectButton('save')->form();
        $crawler = $client->submit($form, array(
            'form[name]' => 'Test article 2',
        ));

        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/admin/articles');
        $this->assertCount(2, $crawler->filter('#articles tbody tr'));
        $this->assertEquals('Test article 2', $crawler->filter('table#articles tbody tr:first-child td:first-child')->text());
    }

    public function testDeleteArticle()
    {
        $this->app['db']->executeUpdate("INSERT INTO article (page_id, name, slug, content, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)", array(
            1,
            "Test article",
            "test-article",
            "",
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
        ));

        $client = $this->createClient();
        $this->logIn($client);
        $crawler = $client->request('GET', '/admin/articles/delete/1');

        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/admin/articles');
        $this->assertCount(1, $crawler->filter('#articles tbody tr'));
    }

}
