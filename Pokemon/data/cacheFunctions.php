<?php
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

function genData($i)
{
    $url = "https://pokeapi.co/api/v2/pokemon/$i/";
    $detailsJson = file_get_contents($url);
    $details = json_decode($detailsJson, true);
    return $details;
}

function evoData($i)
{
    $speciesUrl = "https://pokeapi.co/api/v2/pokemon-species/$i/";
    $speciesJson = file_get_contents($speciesUrl);
    $speciesDetails = json_decode($speciesJson, true);
    $evoChainUrl = $speciesDetails['evolution_chain']['url'];
    return $evoChainUrl;
}

function encounterData($i)
{
    $encounterUrl = "https://pokeapi.co/api/v2/pokemon/$i/encounters";
    $encounterJson = file_get_contents($encounterUrl);
    $encounterData = json_decode($encounterJson, true);
    return $encounterData;
}

function encounterDetails($encounterData)
{
    $encounters = [];

    foreach ($encounterData as $area) {
        foreach ($area['version_details'] as $versionDetail) {
            foreach ($versionDetail['encounter_details'] as $encounterDetail) {
                $encounters[] = [
                    'location_area' => $area['location_area']['name'],
                    'version' => $versionDetail['version']['name'],
                    'method' => $encounterDetail['method']['name'],
                    'chance' => $encounterDetail['chance']
                ];
            }
        }
    }
    return $encounters;
}
