<?php
// Load cached Pokémon data
$pokemonData = json_decode(file_get_contents(__DIR__ . '/data/PokemonInfo.json'), true);

// Load type chart
$typeChart = json_decode(file_get_contents(__DIR__ . '/data/typechart.json'), true);

function calculateTypeMultiplier(array $attackerTypes, array $defenderTypes, array $chart): float
{
    $multiplier = 1.0;

    foreach ($attackerTypes as $attacker) {
        foreach ($defenderTypes as $defender) {
            if (in_array($defender, $chart[$attacker]['strong'])) {
                $multiplier *= 2;
            } elseif (in_array($defender, $chart[$attacker]['weak'])) {
                $multiplier *= 0.5;
            } elseif (in_array($defender, $chart[$attacker]['immune'])) {
                $multiplier *= 0;
            }
        }
    }

    return $multiplier;
}

function findPokemonByName(string $name, array $pokemonList): ?array
{
    foreach ($pokemonList as $pokemon) {
        if (strtolower($pokemon['name']) === strtolower($name)) {
            return $pokemon;
        }
    }
    return null;
}

function getTypeEffectLabel(float $multiplier): string
{
    if ($multiplier > 1) return "strong";
    if ($multiplier < 1 && $multiplier > 0) return "weak";
    if ($multiplier == 0) return "immune";
    return "neutral";
}


$attackerName = "charizard";
$defenderName = "squirtle";

$attacker = findPokemonByName($attackerName, $pokemonData);
$defender = findPokemonByName($defenderName, $pokemonData);

if (!$attacker || !$defender) {
    die("Pokémon not found.");
}

$multiplier = calculateTypeMultiplier($attacker['types'], $defender['types'], $typeChart);
$result = getTypeEffectLabel($multiplier);

echo ucfirst($attacker['name']) . " vs " . ucfirst($defender['name']) . ": $result (x$multiplier)";
