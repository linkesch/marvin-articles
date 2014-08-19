<?php

namespace Marvin\Articles\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function () use ($app) {
            $articles = $app['db']->fetchAll("SELECT a.*, p.name AS page FROM article a LEFT JOIN page p ON p.id = a.page_id ORDER BY a.id DESC");

            return $app['twig']->render('admin/articles/list.twig', array(
                'articles' => $articles,
            ));
        })
        ->bind('admin_articles');

        $controllers->match('/form/{id}', function (Request $request, $id) use ($app) {
            $articleData = array();

            if ($id > 0) {
                $articleData = $app['db']->fetchAssoc("SELECT * FROM article WHERE id = ?", array($id));
            }

            $pagesData = $app['db']->fetchAll("SELECT * FROM page ORDER BY sort ASC");
            $pages = array();
            foreach ($pagesData as $key => $value) {
                $pages[$value['id']] = $value['name'];
            }

            $form = $app['form.factory']->createBuilder('form', $articleData)
                ->add('id', 'hidden')
                ->add('name', 'text')
                ->add('content', 'textarea', array(
                    'required' => false,
                ))
                ->add('page_id', 'choice', array(
                    'label' => 'Page',
                    'empty_value' => '',
                    'choices' => $pages,
                    'required' => false,
                ))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                $slug = $originalSlug = $app['slugify']->slugify($data['name']);
                $i = 2;
                do {
                    $find = $app['db']->fetchAssoc("SELECT COUNT(*) AS count FROM article WHERE slug = ?". ($data['id'] > 0 ? " AND id != ". $data['id'] : ""), array($slug));
                    if ($find['count'] > 0) {
                        $slug = $originalSlug .'-'. $i;
                        $i++;
                    }
                } while ($find['count'] > 0);

                if ($data['id'] == 0) {
                    $app['db']->executeUpdate("INSERT INTO article (page_id, name, slug, content, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)", array(
                        $data['page_id'],
                        $data['name'],
                        $slug,
                        $data['content'],
                        date('Y-m-d H:i:s'),
                        date('Y-m-d H:i:s'),
                    ));

                    $data['id'] = $app['db']->lastInsertId();

                    $app['session']->getFlashBag()->add('message', $app['translator']->trans('The new article was added.'));
                } else {
                    $app['db']->executeUpdate("UPDATE article SET page_id = ?, name = ?, slug = ?, content = ?, updated_at = ? WHERE id = ?", array(
                        $data['page_id'],
                        $data['name'],
                        $slug,
                        $data['content'],
                        date('Y-m-d H:i:s'),
                        $data['id'],
                    ));

                    $app['session']->getFlashBag()->add('message', $app['translator']->trans('Changes were saved.'));
                }

                return $app->redirect('/admin/articles/form/'. $data['id']);
            }

            return $app['twig']->render('admin/articles/form.twig', array(
                'form' => $form->createView(),
            ));
        })
        ->value('id', 0)
        ->assert('id', '\d+');

        $controllers->get('/delete/{id}', function ($id) use ($app) {
            $app['db']->delete('article', array('id' => $id));

            $app['session']->getFlashBag()->add('message', $app['translator']->trans('The article was deleted'));

            return $app->redirect('/admin/articles');
        })
        ->assert('id', '\d+');

        return $controllers;
    }
}
