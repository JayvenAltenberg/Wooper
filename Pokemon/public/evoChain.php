<?php

function getEvolutionChain(string $pokemonName): array {
    $cacheFile = __DIR__ . '/../data/PokemonInfo.json';
    
    if (!file_exists($cacheFile)) {
        return ['error' => 'Cache not found'];
    }

    $allPokemon = json_decode(file_get_contents($cacheFile), true);

    $startPokemon = null;
    foreach ($allPokemon as $pokemon) {
        if ($pokemon['name'] === strtolower($pokemonName)) {
            $startPokemon = $pokemon;
            break;
        }
    }

    if (!$startPokemon) {
        return ['error' => 'Pokemon not found'];
    }

    $evolutionChain = [$startPokemon['name']];
    $current = $startPokemon;
    while (!empty($current['evolves_into_id'])) {
        $next = array_filter($allPokemon, fn($p) => $p['id'] === $current['evolves_into_id']);
        if (!$next) break;
        $current = array_values($next)[0];
        $evolutionChain[] = $current['name'];
    }

    return [
        'pokemon' => $pokemonName,
        'evolution_chain' => $evolutionChain
    ];
}
