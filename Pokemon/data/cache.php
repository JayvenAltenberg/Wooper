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


function fetchKantoPokemon(int $limit = 151): array
{
    $allPokemon = [];

    for ($i = 1; $i <= $limit; $i++) {
        $url = "https://pokeapi.co/api/v2/pokemon/$i/";
        $detailsJson = file_get_contents($url);
        $details = json_decode($detailsJson, true);

        $speciesUrl = "https://pokeapi.co/api/v2/pokemon-species/$i/";
        $speciesJson = file_get_contents($speciesUrl);
        $speciesDetails = json_decode($speciesJson, true);
        $evoChainUrl = $speciesDetails['evolution_chain']['url'];

        $evolvesIntoId = getEvolutionTarget($evoChainUrl, $i);

        // Extract moves from the kanto region
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
            'moves' => $moves
        ];

        //slow down requests to avoid hitting API limits
        usleep(600000);
    }

    return $allPokemon;
}

// Fetch Pokémon and cache to JSON
$allPokemon = fetchKantoPokemon(151);
file_put_contents($cacheFile, json_encode($allPokemon, JSON_PRETTY_PRINT));

echo "Kanto Pokémon data cached in $cacheFile\n";
