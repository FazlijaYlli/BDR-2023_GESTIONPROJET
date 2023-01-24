<?php
require_once $_SERVER['DOCUMENT_ROOT']."/settings.php";

$GLOBALS["db"] = pg_connect($host. ' ' . $port. ' ' .$dbname.' '.$credentials);

// Requête pour récupérer les informations d'un projet spécifique
function getProjetInfo(string $nomProjet)
{
    $query = "SELECT nom, description FROM projet WHERE nom=$1";
    $params = array($nomProjet);

    $result = pg_query_params($GLOBALS["db"], $query, $params);
    return $result;
}

function getProjets()
{
    $query = "SELECT nom, description FROM projet";
    $params = array();

    $result = pg_query_params($GLOBALS["db"], $query, $params);
    return $result;
}

function getUserWithCredential($username, $password)
{
    $hash = hash("sha512", $password);
    $query = "SELECT id, nom, prénom FROM utilisateur WHERE  CONCAT (prénom,'.', nom) = $1 AND hashmdp = $2";
    $params = array($username,$hash);

    $result = pg_query_params($GLOBALS["db"], $query, $params);
    return $result;
}


// Requête pour récupérer les informations d'une release spécifique
function getReleaseInfo(string $nomProjet, string $nomRelease)
{
    $query = "SELECT * FROM projetrelease WHERE nomprojet=$1 AND nom=$2 ";
    $params = array($nomProjet, $nomRelease);

    $result = pg_query_params($GLOBALS["db"], $query, $params);
    return $result;
}


// Requête pour récupérer les informations d'une liste de tache d'une release
function getListTacheInfo(string $nomProjet, string $nomRelease)
{
    $query = "SELECT * FROM tâche WHERE nomprojet=$1 AND nomprojetrelease=$2 ORDER BY statut ASC";
    $params = array($nomProjet,$nomRelease);

    $result = pg_query_params($GLOBALS["db"], $query, $params);
    return $result;
}


// Requête pour récupérer les informations d'une tâche
function getTacheInfo(string $idTache)
{
    $query = "SELECT * FROM tâche WHERE id=$1";
    $params = array($idTache);

    $result = pg_query_params($GLOBALS["db"], $query, $params);
    return $result;
}

