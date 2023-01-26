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

function getProjets($idUser)
{

    $query = "SELECT DISTINCT projet.nom AS nom FROM projet
        LEFT JOIN utilisateur_projet
        ON projet.nom = utilisateur_projet.nomprojet
        CROSS JOIN utilisateur
        WHERE utilisateur.id = $idUser
        AND (utilisateur.id = utilisateur_projet.idutilisateur OR utilisateur.fonction = 'Directeur')";
    $params = array();

    $result = pg_query_params($GLOBALS["db"], $query, $params);
    return $result;
}

// Requête pour récupérer les informations d'une liste de releases
function getReleases(string $nomProjet)
{
    $query = "SELECT nom, nomprojet, sortieeffective, sortieprévue FROM projetrelease WHERE nomprojet = $1";
    $params = array($nomProjet);

    $result = pg_query_params($GLOBALS["db"], $query, $params);
    return $result;
}

function getUserWithCredential($username, $password)
{
    $hash = hash("sha512", $password);
    $query = "SELECT id, nom, prénom, fonction FROM utilisateur WHERE  CONCAT (prénom,'.', nom) = $1 AND hashmdp = $2";
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
function getTaches(string $nomProjet, string $nomRelease)
{
    $query = "SELECT * FROM tâche WHERE nomprojet=$1 AND nomprojetrelease=$2 ORDER BY delai ASC";
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

function createProjet($name, $description, $responsable)
{
    $query = "INSERT INTO Projet (nom, description)
        VALUES ('$name', '$description')";
    pg_query($GLOBALS["db"], $query);
    addToProject($name, $responsable, 'Responsable');
}

function getUsersRoleForProjet($nomProjet){
    $query = "SELECT utilisateur.id, utilisateur.prénom, utilisateur.nom, utilisateur_projet.responsabilité FROM utilisateur
        LEFT JOIN utilisateur_projet
        ON utilisateur_projet.idutilisateur = utilisateur.id AND utilisateur_projet.nomprojet = $1";
    $params = array($nomProjet);
    $result = pg_query_params($GLOBALS["db"], $query, $params);
    return $result;
}
function createRelease($nomProjet, $nomRelease, $estimatedDate)
{
    $query = "INSERT INTO projetrelease (nomprojet, nom, sortieprévue)
        VALUES ('$nomProjet', '$nomRelease','$estimatedDate')";
    pg_query($GLOBALS["db"], $query);
}


function getUsers()
{
    $query = "SELECT utilisateur.id, utilisateur.prénom, utilisateur.nom FROM utilisateur";
    $result = pg_query($GLOBALS["db"], $query);
    return $result;
}

function getGroupesTache()
{
    $query = "SELECT nom FROM groupedetâche";
    $result = pg_query($GLOBALS["db"], $query);
    return $result;
}


function addToProject($projetname, $userIdToAdd, $role)
{
    $query = "INSERT INTO Utilisateur_Projet (idUtilisateur, nomProjet, responsabilité) VALUES ($1, $2, $3)";
    $params = array($userIdToAdd, $projetname, $role);
    pg_query_params($GLOBALS["db"], $query, $params);
}
function getUserRole($idUtilisateur, $nomProjet)
{
    $query = "SELECT responsabilité FROM utilisateur_projet WHERE nomprojet=$1 AND idutilisateur=$2";
    $params = array($nomProjet, $idUtilisateur);
    return pg_query_params($GLOBALS["db"], $query, $params);
}

function createTache($titre, $description, $delai, $dureeestimée, $groupeTache, $nomprojet, $nomrelease)
{
    $query = "INSERT INTO Tâche (titre, description, delai, statut, dureeestimée, nomgroupedetâche, nomprojet, nomprojetrelease)
        VALUES ($1,$2,$3,$4,$5,$6,$7,$8)";
    $params = array($titre, $description, $delai, 'Planifié', $dureeestimée,  $groupeTache, $nomprojet, $nomrelease);
    pg_query_params($GLOBALS["db"], $query, $params);
}

function updateTacheStatus($type,$idTache, $idUser)
{
    switch ($type){
        case 'terminer':
            $query = "UPDATE tâche SET statut = $2, dureeréelle = NOW() WHERE id = $1";
            $params = array($idTache,'Terminé');
            break;
        case 'assigner':
            $query = "UPDATE tâche SET idutilisateur = $2, statut = $3 WHERE id = $1";
            $params = array($idTache,$idUser,'En cours');
            break;
        default:
            exit();
    }
    pg_query_params($GLOBALS["db"], $query, $params);
}

function terminateRelease() {
    $query = "UPDATE projetrelease SET sortieeffective = NOW() WHERE nomProjet = $1 AND nom = $2";
    $params = array($_GET['projet'],$_GET['release']);
    pg_query_params($GLOBALS["db"], $query, $params);
}