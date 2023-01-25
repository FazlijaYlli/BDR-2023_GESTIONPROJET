<?php

$msgTodisplay = '';
$displayError = false;

function home(){
    require 'views/home.php';
}

function projet(){
    $result = getProjetInfo($_GET['projet']);

    if (!$result) {
        echo "Une erreur est survenue lors de la récupération des informations du projet.\n";
        exit;
    }

    // Récupère les informations du projet
    $projet = pg_fetch_assoc($result);

    require 'views/projet.php';

    listUser($_GET['projet']);

    releaseList($_GET['projet']);
}


function projetList()
{
    $result = getProjets($_SESSION['userid']);
    $projets = pg_fetch_all($result);

    if (!$projets) {
        $noRessource = "projets";
        require 'views/noRessource.php';
        exit;
    } else {
        require 'views/projetList.php';
    }

    if($_SESSION['admin']){
        $resultUser = getUsers();
        $users = pg_fetch_all($resultUser);
        require 'views/addProjet.php';
    }
}

function releaseList(string $nomProjet)
{
    $result = getReleases($nomProjet);

    if (!$result) {
        require 'views/error.php';
        exit;
    }

    $roleQuery = getUserRole($_SESSION['userid'], $_GET['projet']);
    if(!$roleQuery) {
        require 'views/error.php';
        exit;
    }

    $roleFetch = pg_fetch_assoc($roleQuery);

    if(!$roleFetch) {
        require 'views/noRessource.php';
        exit;
    }

    $responsable = $roleFetch['responsabilité'] == "Responsable";

    $releases = pg_fetch_all($result);

    if (!$releases) {
        $noRessource = "releases";
        require 'views/noRessource.php';
        exit;
    } else {
        require 'views/releaseList.php';
    }
}

function listUser(String $projet)
{
    $result = getUsersRoleForProjet($projet);

    if (!$result) {
        require 'views/error.php';
        return false;
    }

    $users = pg_fetch_all($result);
    require 'views/userList.php';

    if($_SESSION['admin']){
        require 'views/addUser.php';
    }
}

function tacheList(string $projet, string $release){
    $result = getTaches($projet, $release);

    if (!$result) {
        require 'views/error.php';
        return false;
    }

    $taches = pg_fetch_all($result);

    if (!$taches) {
        $noRessource = "tâches";
        require 'views/noRessource.php';
        exit;
    } else {
        require 'views/tacheList.php';
    }
}
function release(): void
{
    $result = getReleaseInfo($_GET['projet'],$_GET['release']);

    if (!$result) {
        require 'views/error.php';
        exit;
    }

    $release = pg_fetch_assoc($result);

    if (!$release) {
        require 'views/unknown.php';
        exit;
    }

    require 'views/release.php';

    tacheList($_GET['projet'],$_GET['release']);
}

function tache(): void
{
    $result = getTacheInfo($_GET['id']);

    if (!$result) {
        require 'views/error.php';
        exit;
    }

    $tache = pg_fetch_assoc($result);

    if (!$tache) {
        require 'views/unknown.php';
        exit;
    }

    require 'views/tache.php';

}

function custom(){
    if(isset($_GET['id'])){
        require 'views/custom.php';
    }else{
        $msg = "id not set";
        require 'views/error.php';
    }
}


function login(){
    if(isset($_POST['usr'])&&isset($_POST['psw'])){
        trylogin();
    }else{
        displayLoginPage();
    }
}

function logout(){
    unset($_SESSION['userid']);
    header('Location: ?action=login');
}


function displayLoginPage(){
    require 'views/login.php';
}

function tryLogin(){
    $res = checkPassword($_POST['usr'], $_POST['psw']);
    if(!$res){
        echo "Mauvais identifient ou mot de passe";
        unset($_POST['usr']);
        unset($_POST['psw']);
        login();
    }else{
        $_SESSION['userid'] = $res['id'];
        $_SESSION['admin'] = $res['fonction'] == 'Directeur';
        echo "Vous êtes connecté";
        header('Location: ?action=projetList');
    }
}

function checkPassword($username, $password)
{
    $result = getUserWithCredential($username, $password);
    $user = pg_fetch_assoc($result);
    return $user;
}

function newprojet(){
    if($_POST['responsable'] != -1){
        createProjet($_POST['nameP'],$_POST['descriptionP'],$_POST['responsable']);
    }
    header('Location: ?action=projetList');
}

function newrelease(){
    $date = $_POST['estimatedDate'];
    createRelease($_GET['projet'],$_POST['nameR'],$date);
    header('Location: ?action=projet&projet='.$_GET['projet']);
}
function addUser(){
    addToProject($_POST['projetname'],$_POST['userIdToAdd'],$_POST['role']);
    header('Location: ?action=projet&projet='.$_POST['projetname']);
}

