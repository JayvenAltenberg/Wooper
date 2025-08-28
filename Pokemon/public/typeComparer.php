<?php

$pokemonFile = __DIR__ . '/../data/PokemonInfo.json';
$typeChartFile = __DIR__ . '/../data/typechart.json';


if (!file_exists($pokemonFile)) die("PokemonInfo.json not found.\n");
if (!file_exists($typeChartFile)) die("typechart.json not found.\n");


$pokemonData = json_decode(file_get_contents($pokemonFile), true);
$typeChart = json_decode(file_get_contents($typeChartFile), true);


if (!is_array($pokemonData)) die("Failed to decode PokemonInfo.json.\n");
if (!is_array($typeChart)) die("Failed to decode typechart.json.\n");


function calculateTypeMultiplier($attackerTypes, $defenderTypes, $chart)
{
    $multiplier = 1;
    foreach ($attackerTypes as $attacker) {
        foreach ($defenderTypes as $defender) {
            if (in_array($defender, $chart[$attacker]['strong'])) $multiplier *= 2;
            elseif (in_array($defender, $chart[$attacker]['weak'])) $multiplier *= 0.5;
            elseif (in_array($defender, $chart[$attacker]['immune'])) $multiplier *= 0;
        }
    }
    return $multiplier;
}

function findPokemonByName($name, $pokemonList)
{
    foreach ($pokemonList as $p) {
        if (isset($p['name']) && strtolower($p['name']) === strtolower($name)) return $p;
    }
    return null;
}


function getTypeEffectLabel($multiplier)
{
    if ($multiplier > 1) return "strong";
    if ($multiplier < 1 && $multiplier > 0) return "weak";
    if ($multiplier == 0) return "immune";
    return "neutral";
}


echo "Pokémon 1 name: ";
$attackerName = trim(fgets(STDIN));

echo "Pokémon 2 name: ";
$defenderName = trim(fgets(STDIN));

$attacker = findPokemonByName($attackerName, $pokemonData);
$defender = findPokemonByName($defenderName, $pokemonData);

if (!$attacker || !$defender) {
    die("One or both Pokémon not found. Check spelling.\n");
}

$multiplier = calculateTypeMultiplier($attacker['types'], $defender['types'], $typeChart);
$result = getTypeEffectLabel($multiplier);

echo ucfirst($attacker['name']) . " vs " . ucfirst($defender['name']) . ": $result (x$multiplier)\n";
