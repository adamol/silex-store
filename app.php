<?php

require_once __DIR__.'/bootstrap.php';

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Products\Repository as ProductsRepository;

$app = new Silex\Application();

$app['debug'] = true;

$app['db'] = function($app) {
    $dsn = 'mysql:host=localhost;dbname=silex_store';
    $user = 'root';
    $pass = 'root';

    return  new PDO($dsn, $user, $pass);
};

$app[ProductsRepository::class] = function($app) {
    return new App\Products\Repository($app['db']);
};

$app->get('/products', function(Silex\Application $app) {
    $products = $app[ProductsRepository::class]->all();

    return new JsonResponse($products);
});

$app->get('/products/{productId}', function(Silex\Application $app, $productId) {
    $product = $app[ProductsRepository::class]->findById($productId);

    if (! $product) {
        return new JsonResponse([
            'error' => [
                'message' => 'No Records Found.'
            ]
        ]);
    }

    return new JsonResponse($product);
});

return $app;
