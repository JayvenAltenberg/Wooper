<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->get('/swagger.json', function ($request, $response) {
    $json = file_get_contents(__DIR__ . '/swagger.json');
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/cache', function ($request, $response) {
    $cacheFile = __DIR__ . '/../data/PokemonInfo.json';

    if (!file_exists($cacheFile)) {
        $response->getBody()->write(json_encode(['error' => 'Cache not found']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    $data = file_get_contents($cacheFile);
    $response->getBody()->write($data);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/evolutions', function ($request, $response) {
    require __DIR__ . '/evoChain.php';

    $queryParams = $request->getQueryParams();
    $pokemonName = $queryParams['name'] ?? '';

    $result = getEvolutionChain($pokemonName);

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/typeCompare', function($request, $response) {

});

$app->run();