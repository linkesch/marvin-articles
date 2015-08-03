<?php

namespace Marvin\Articles\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class FrontendServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->extend('pages_plugins', function ($plugins) use ($app) {

            $plugins['articles'] = function ($pageId) use ($app) {
                $articles = $app['db']->fetchAll("SELECT * FROM article WHERE page_id = ? ORDER BY sort DESC", array($pageId));

                if ($articles) {
                    return array(
                        'template' => $app['config']['theme'] .'/articles.twig',
                        'articles' => $articles,
                    );
                } else {
                    return null;
                }
            };

            return $plugins;
        });
    }

    public function boot(Application $app)
    {
    }
}
