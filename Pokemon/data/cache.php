<?php

$cacheFile = 'data/PokemonInfo.json';
$allDetailedPokemon = [];

if (!file_exists($cacheFile)) {
    die("File not found: $cacheFile");
};

for ($i = 1; $i < 151; $i++) {
    $alreadyExists = array_filter($allDetailedPokemon, fn($p) => $p['id'] === $i);
    if ($alreadyExists) {
        continue;
    }

    $url = "https://pokeapi.co/api/v2/pokemon/$i/";
    $detailsJson = file_get_contents($url);
    $details = json_decode($detailsJson, true);

    // Make the format for what to grab from the api
    $allDetailedPokemon[] = [
        'id' => $details['id'],
        'name' => $details['name'],
        'height' => $details['height'],
        'weight' => $details['weight'],
        'base_experience' => $details['base_experience'],
        'types' => array_map(fn($type) => $type['type']['name'], $details['types']),
        'abilities' => array_map(fn($ability) => $ability['ability']['name'], $details['abilities']),
        'sprites' => [
            'front_default' => $details['sprites']['front_default'],
            'front_shiny' => $details['sprites']['front_shiny']
        ],
        'stats' => array_map(function ($stat) {
            return [
                'name' => $stat['stat']['name'],
                'base_stat' => $stat['base_stat']
            ];
        }, $details['stats'])
    ];
    usleep(600000);
}
file_put_contents($cacheFile, json_encode($allDetailedPokemon, JSON_PRETTY_PRINT));
