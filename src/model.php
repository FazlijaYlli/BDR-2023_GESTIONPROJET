<?php
require_once $_SERVER['DOCUMENT_ROOT']."/settings.php";

$GLOBALS["db"] = pg_connect($host. ' ' . $port. ' ' .$dbname.' '.$credentials);

// Requête pour récupérer les informations d'un projet spécifique
function getProjetInfo(string $nomProjet)
{
    $query = "SELECT nom, description FROM projet WHERE nom='$nomProjet'";
    $result = pg_query($GLOBALS["db"], $query);
    return $result;
}

function getProjets()
{
    $query = "SELECT nom, description FROM projet";
    $result = pg_query($GLOBALS["db"], $query);
    return $result;
}