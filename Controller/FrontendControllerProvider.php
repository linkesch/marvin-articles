<?php

namespace Marvin\Articles\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class FrontendControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/{pageSlug}/article/{articleSlug}', function (Request $request, $pageSlug, $articleSlug) use ($app) {
            $article = $app['db']->fetchAssoc(
                "SELECT a.*, p.slug AS pageSlug
                FROM article a
                LEFT JOIN page p ON p.id = a.page_id
                WHERE a.slug = ?"
            , array($articleSlug));

            $before = $app['db']->fetchAssoc(
                "SELECT a.*, p.slug AS pageSlug
                FROM article a
                LEFT JOIN page p ON p.id = a.page_id
                WHERE a.id < ? AND p.slug = ?
                ORDER BY sort DESC
                LIMIT 0,1"
            , array($article['id'], $pageSlug));

            $after = $app['db']->fetchAssoc(
                "SELECT a.*, p.slug AS pageSlug
                FROM article a
                LEFT JOIN page p ON p.id = a.page_id
                WHERE a.id > ? AND p.slug = ?
                ORDER BY sort ASC
                LIMIT 0,1"
            , array($article['id'], $pageSlug));

            if (!$article) {
                $app->abort(404, 'Article "'. $articleSlug .'" does not exist.');
            }

            $request->query->set('slug', $pageSlug);

            return $app['twig']->render($app['config']['theme'] .'/article.twig', array(
                'article' => $article,
                'before' => $before,
                'after' => $after,
            ));
        });

        $controllers->get('/rss', function () use ($app) {
            $articles = $app['db']->fetchAll(
                "SELECT a.*, p.slug AS pageSlug
                FROM article a
                LEFT JOIN page p ON p.id = a.page_id
                ORDER BY a.id DESC"
            );

            return $app['twig']->render('frontend/articles/rss.twig', array(
                'articles' => $articles,
            ));
        });

        return $controllers;
    }
}
