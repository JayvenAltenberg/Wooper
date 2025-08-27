<?php

$cacheFile = 'data/PokemonInfo.json';
$url = "https://pokeapi.co/api/v2/pokemon?offset=0&limit=3";
$allDetailedPokemon = [];

if (!file_exists($cacheFile)) {
    die("File not found: $cacheFile");
};

function getEvolutionTarget($evolutionChainUrl, $currentPokemonId)
{
    $evolutionJson = file_get_contents($evolutionChainUrl);
    $evolutionData = json_decode($evolutionJson, true);



    $result = findEvolutionTarget($evolutionData['chain'], $currentPokemonId);
    return $result === false ? null : $result;
}

function findEvolutionTarget($chain, $searchId)
{
    $speciesUrl = $chain['species']['url'];
    $currentId = (int) basename(rtrim($speciesUrl, '/'));

    if ($currentId === $searchId) {
        if (!empty($chain['evolves_to'])) {
            $nextEvolutionUrl = $chain['evolves_to'][0]['species']['url'];
            return (int) basename(rtrim($nextEvolutionUrl, '/'));
        } else {
            return null;
        }
    }

    foreach ($chain['evolves_to'] as $evolution) {
        $result = findEvolutionTarget($evolution, $searchId);
        if ($result !== false) {
            return $result;
        }
    }

    return false;
}

for ($i = 1; $i < 21; $i++) {

    $url = "https://pokeapi.co/api/v2/pokemon/$i/";
    $DetailsJson = file_get_contents($url);
    $Details = json_decode($DetailsJson, true);

    $SpeciesUrl = "https://pokeapi.co/api/v2/pokemon-species/$i/";
    $SpeciesDetailsJson = file_get_contents($SpeciesUrl);
    $SpeciesDetails = json_decode($SpeciesDetailsJson, true);
    $EvoChain = $SpeciesDetails['evolution_chain']['url'];


    $evolutesIntoId = getEvolutionTarget($EvoChain, $i);

    $allDetailedPokemon[] = [
        'id' => $Details['id'],
        'name' => $Details['name'],
        'height' => $Details['height'],
        'weight' => $Details['weight'],
        'base_experience' => $Details['base_experience'],
        'types' => array_map(fn($type) => $type['type']['name'], $Details['types']),
        'abilities' => array_map(fn($ability) => $ability['ability']['name'], $Details['abilities']),
        'evolves_into_id' => $evolutesIntoId,
        'sprites' => [
            'front_default' => $Details['sprites']['front_default'],
            'front_shiny' => $Details['sprites']['front_shiny']
        ],
        'stats' => array_map(function ($stat) {
            return [
                'name' => $stat['stat']['name'],
                'base_stat' => $stat['base_stat']
            ];
        }, $Details['stats'])
    ];
}
file_put_contents($cacheFile, json_encode($allDetailedPokemon, JSON_PRETTY_PRINT));
