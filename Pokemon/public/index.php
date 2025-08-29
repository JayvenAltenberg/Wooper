<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

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

$app->get('/typeMatchup', function ($request, $response) {
    require __DIR__ . '/typeComparer.php';
    $queryParams = $request->getQueryParams();
    $attacker = $queryParams['attacker'] ?? '';
    $defender = $queryParams['defender'] ?? '';

    $result = calculateMatchup($attacker, $defender);

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/moveset', function ($request, $response) {
    require __DIR__ . '/../routes/Moves.php';

    $queryParams = $request->getQueryParams();
    $pokemonName = $queryParams['name'] ?? '';

    $result = getMoves($pokemonName);

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/encounters', function ($request, $response) {
    require __DIR__ . '/encounters.php';

    $queryParams = $request->getQueryParams();
    $name = $queryParams['name'] ?? '';
    $gameVersion = $queryParams['game-version'] ?? '';

    $result = findEncounters($name, $gameVersion);

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/stat', function ($request, $response) {
    require __DIR__ . '/statCheck.php';

    $queryParams = $request->getQueryParams();
    $stat = $queryParams['stat'];

    $result = highestStat($stat);

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
