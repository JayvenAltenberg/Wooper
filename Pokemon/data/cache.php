<?php

$cacheFile = 'data/PokemonInfo.json';
$url = "https://pokeapi.co/api/v2/pokemon?offset=0&limit=3";
$allDetailedPokemon = [];
require __DIR__ . '/cacheFunctions.php';

if (!file_exists($cacheFile)) {
    die("File not found: $cacheFile");
};



function fetchKantoPokemon(int $limit = 151): array
{
    $allPokemon = [];

    $cacheFile = __DIR__ . '/data/PokemonInfo.json';
    $existingData = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : [];
    $existingById = [];

    foreach ($existingData as $p) {
        $existingById[$p['id']] = $p;
    }

    for ($i = 1; $i <= $limit; $i++) {
        if (isset($existingById[$i])) {
            $allPokemon[] = $existingById[$i];
            continue;
        }

        $details = genData($i);
        $evoChainUrl = evoData($i);
        $encounterData = encounterData($i);

        $evolvesIntoId = getEvolutionTarget($evoChainUrl, $i);
        $encounters = encounterDetails($encounterData);

        $moves = array_map(function ($move) {
            $firstVgd = $move['version_group_details'][0];
            return [
                'name' => $move['move']['name'],
                'method' => $firstVgd['move_learn_method']['name'],
                'level_learned_at' => $firstVgd['level_learned_at']
            ];
        }, $details['moves']);

        $allPokemon[] = [
            'id' => $details['id'],
            'name' => $details['name'],
            'height' => $details['height'],
            'weight' => $details['weight'],
            'base_experience' => $details['base_experience'],
            'types' => array_map(fn($type) => $type['type']['name'], $details['types']),
            'abilities' => array_map(fn($ability) => $ability['ability']['name'], $details['abilities']),
            'evolves_into_id' => $evolvesIntoId,
            'sprites' => [
                'front_default' => $details['sprites']['front_default'],
                'front_shiny' => $details['sprites']['front_shiny']
            ],
            'stats' => array_map(function ($stat) {
                return [
                    'name' => $stat['stat']['name'],
                    'base_stat' => $stat['base_stat']
                ];
            }, $details['stats']),
            'moves' => $moves,
            'locations' => $encounters
        ];

        usleep(600000);
    }

    return $allPokemon;
}

$allPokemon = fetchKantoPokemon(151);
file_put_contents($cacheFile, json_encode($allPokemon, JSON_PRETTY_PRINT));

echo "Kanto Pokémon data cached in $cacheFile\n";
