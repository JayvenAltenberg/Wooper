<?php

$jsonFile = __DIR__ . '/../data/PokemonInfo.json';
$jsonContent = file_get_contents($jsonFile);
$data = json_decode($jsonContent, true);

$id = 58;
$selectedPokemonData = null;

foreach ($data as $pokemon) {
    if ($id === $pokemon["id"]) {
        $selectedPokemonData = $pokemon;
        break;
    };
}

if (isset($selectedPokemonData)) {
    echo $selectedPokemonData['name'];
    print_r($selectedPokemonData['moves']);
}

