<?php

function highestStat($statName)
{
    $cacheFile = __DIR__ . '/../data/PokemonInfo.json';
    $allPokemon = json_decode(file_get_contents($cacheFile), true);

    $highest = -1;
    $topPokemon = [];

    foreach ($allPokemon as $pokemon) {
        foreach ($pokemon['stats'] as $stat) {
            if ($stat['name'] === strtolower($statName)) {
                $value = $stat['base_stat'];
                if ($value > $highest) {
                    $highest = $value;
                    $topPokemon = [[
                        'name' => $pokemon['name'],
                        'stat_value' => $value
                    ]];
                } elseif ($value === $highest) {
                    $topPokemon[] = [
                        'name' => $pokemon['name'],
                        'stat_value' => $value
                    ];
                }
            }
        }
    }

    if ($highest === -1) {
        return ['error' => "Stat '$statName' not found"];
    }

    return [
        'stat' => strtolower($statName),
        'highest_value' => $highest,
        'pokemon' => $topPokemon
    ];
}

