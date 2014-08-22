<?php

// Register service provider
$app->register(new Marvin\Articles\Provider\InstallServiceProvider());
$app->register(new Marvin\Articles\Provider\FrontendServiceProvider());

// Mount plugin controller provider
$app->mount('/admin/articles', new Marvin\Articles\Controller\AdminControllerProvider());
$app->mount('/', new Marvin\Articles\Controller\FrontendControllerProvider());
if ($app['debug']) {
    $app->mount('/install/articles', new Marvin\Articles\Controller\InstallControllerProvider());
}
