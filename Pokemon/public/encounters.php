<?php
function findEncounters(string $pokemonName, ?string $version = null): array {
    $cacheFile = __DIR__ . '/../data/PokemonInfo.json';

    if (!file_exists($cacheFile)) {
        return ['error' => 'Cache not found'];
    }

    $allPokemon = json_decode(file_get_contents($cacheFile), true);

    $pokemon = null;
    foreach ($allPokemon as $p) {
        if ($p['name'] === strtolower($pokemonName)) {
            $pokemon = $p;
            break;
        }
    }

    if (!$pokemon) {
        return ['error' => 'Pokemon not found'];
    }

    $encounters = $pokemon['locations'] ?? [];
    if ($version) {
        $encounters = array_values(array_filter($encounters, fn($e) => $e['version'] === strtolower($version)));
    }

    return [
        'pokemon' => $pokemon['name'],
        'encounters' => $encounters
    ];
}
