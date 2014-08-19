<?php

namespace Marvin\Articles\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class InstallServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->extend('install_plugins', function ($plugins) use ($app) {

            $plugins[] = function () use ($app) {

                $sm = $app['db']->getSchemaManager();
                $schema = new \Doctrine\DBAL\Schema\Schema();

                // Create table article
                $articleTable = $schema->createTable('article');
                $articleTable->addColumn('id', 'integer', array("autoincrement" => true));
                $articleTable->addColumn('page_id', 'integer', array('notnull' => false));
                $articleTable->addColumn('name', 'string');
                $articleTable->addColumn('slug', 'string');
                $articleTable->addColumn('content', 'text', array('notnull' => false));
                $articleTable->addColumn('created_at', 'datetime');
                $articleTable->addColumn('updated_at', 'datetime');
                $articleTable->setPrimaryKey(array("id"));
                $articleTable->addUniqueIndex(array("slug"));
                $sm->createTable($articleTable);

                $messages[] = $app['install_status'](
                    $sm->tablesExist(array('article')),
                    'Article table was created.',
                    'Problem creating article table.'
                );

                // Create article
                $app['db']->executeUpdate("INSERT INTO article (page_id, name, slug, content, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)", array(
                    1,
                    'Hello World!',
                    'hello-world',
                    '<p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Vestibulum id ligula porta felis euismod semper. Maecenas sed diam eget risus varius blandit sit amet non magna.</p>',
                    date('Y-m-d H:i:s'),
                    date('Y-m-d H:i:s'),
                ));

                $article = $app['db']->fetchAssoc("SELECT COUNT(*) AS count FROM article WHERE name = 'Hello World!'");
                $messages[] = $app['install_status'](
                    $article['count'],
                    'The first article was created.',
                    'Problem creating the first article.'
                );

                return $messages;
            };

            return $plugins;
        });
    }

    public function boot(Application $app)
    {
    }
}
