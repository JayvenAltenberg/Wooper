<?php

function calculateMatchup(string $attackerName, string $defenderName): array {
    $pokemonFile = __DIR__ . '/../data/PokemonInfo.json';
    $typeChartFile = __DIR__ . '/../data/typechart.json';

    if (!file_exists($pokemonFile)) return ['error' => 'PokemonInfo.json not found'];
    if (!file_exists($typeChartFile)) return ['error' => 'typechart.json not found'];

    $pokemonData = json_decode(file_get_contents($pokemonFile), true);
    $typeChart = json_decode(file_get_contents($typeChartFile), true);

    if (!is_array($pokemonData) || !is_array($typeChart)) {
        return ['error' => 'Failed to decode data'];
    }

    $findPokemonByName = function($name, $pokemonList) {
        foreach ($pokemonList as $p) {
            if (isset($p['name']) && strtolower($p['name']) === strtolower($name)) {
                return $p;
            }
        }
        return null;
    };

    $getTypeEffectLabel = function($multiplier) {
        if ($multiplier == 4) return "super effective (x4)";
        if ($multiplier == 2) return "effective (x2)";
        if ($multiplier == 1) return "neutral (x1)";
        if ($multiplier == 0.5) return "not very effective (x0.5)";
        if ($multiplier == 0.25) return "super ineffective (x0.25)";
        if ($multiplier == 0) return "immune (x0)";
        return "neutral (x$multiplier)";
    };

    $attacker = $findPokemonByName($attackerName, $pokemonData);
    $defender = $findPokemonByName($defenderName, $pokemonData);

    if (!$attacker || !$defender) {
        return ['error' => 'One or both Pokémon not found'];
    }

    $multiplier = 1;
    foreach ($attacker['types'] as $atk) {
        foreach ($defender['types'] as $def) {
            if (in_array($def, $typeChart[$atk]['strong'])) {
                $multiplier *= 2;
            } elseif (in_array($def, $typeChart[$atk]['resists'])) {
                $multiplier *= 0.5;
            } elseif (in_array($def, $typeChart[$atk]['immune'])) {
                $multiplier *= 0;
            }
        }
    }

    return [
        'attacker' => $attacker['name'],
        'defender' => $defender['name'],
        'multiplier' => $multiplier,
        'result' => $getTypeEffectLabel($multiplier)
    ];
}
