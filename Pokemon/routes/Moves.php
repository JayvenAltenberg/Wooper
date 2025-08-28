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
        return[
            'name' => $selectedPokemonData['name'],
            'moves' => $selectedPokemonData['moves']
        ];
    // if the pokemon isnt found show a error
    } else {
        return[
            'error' => "Pokémon '{$pokemonName}' not found"
        ];
    }
}
