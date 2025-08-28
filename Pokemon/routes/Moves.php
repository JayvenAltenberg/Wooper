<?php

$app->get('/moveset/{name}', function ($request, $response, $args) {
    $args['name'];
    $jsonFile = __DIR__ . '/../data/PokemonInfo.json';
// Read all the json content
    $jsonContent = file_get_contents($jsonFile);
    $data = json_decode($jsonContent, true);
    $selectedPokemonData = null;

    // loop trough every pokemon in the json till you find the right pokemon
    foreach ($data as $pokemon) {
        if ($args['name'] === $pokemon["name"]) {
            $selectedPokemonData = $pokemon;
            break;
        };
    }
    if ($selectedPokemonData) {
        $response->getBody()->write(json_encode([
            'name' => $selectedPokemonData['name'],
            'moves' => $selectedPokemonData['moves']
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    // if the pokemon isnt found show a error
    } else {
        $response->getBody()->write(json_encode([
            'error' => "Pokémon '{$args['name']}' not found"
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(404);
    }
});
