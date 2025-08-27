<?php

// select a specific pokemon
$id = 58;
$selectedPokemonData = null;

foreach ($data as $pokemon) {
    if ($id === $pokemon["id"]) {
        $selectedPokemonData = $pokemon;
        break;
    };
}

if (isset($selectedPokemonData)) {
    echo $selectedPokemonData['name'];
    print_r($selectedPokemonData['moves']);
}


// make a api call to show a specefic pokemon
$app->get('/moveset/{name}', function ($request, $response, $args) {
    $args['name'];
    $jsonFile = __DIR__ . '/../data/PokemonInfo.json';
// Read all the json content
    $jsonContent = file_get_contents($jsonFile);
    $data = json_decode($jsonContent, true);
});
// what route for the api call

//return what is needed

// show a error if it doesnt work can use status codes
