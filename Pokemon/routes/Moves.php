<?php

function getMoves(string $pokemonName): array
{
    $jsonFile = __DIR__ . '/../data/PokemonInfo.json';
// Read all the json content
    $jsonContent = file_get_contents($jsonFile);
    $data = json_decode($jsonContent, true);
    $selectedPokemonData = null;

    // loop trough every pokemon in the json till you find the right pokemon
    foreach ($data as $pokemon) {
        if ($pokemon["name"] === strtolower($pokemonName)) {
            $selectedPokemonData = $pokemon;
            break;
        };
    }
    if ($selectedPokemonData) {
        $groupedMoves = [
            'level-up' => [],
            'machine' => [],
            'egg' => [],
            'tutor' => []
        ];

        foreach ($selectedPokemonData['moves'] as $move) {
            $method = $move['method'];
            // Only include known categories, you can add more if needed
            if (isset($groupedMoves[$method])) {
                $groupedMoves[$method][] = $move;
            }
        }

        $responseData = [
            'name' => $selectedPokemonData['name'],
            'moves' => $groupedMoves
        ];

        return $responseData;
    // if the pokemon isnt found show a error
    } else {
        return[
        'error' => "Pokémon '{$pokemonName}' not found"
        ];
    }
}
