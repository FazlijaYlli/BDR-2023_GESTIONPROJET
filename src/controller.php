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

    releaseList($_GET['projet']);
}

function projetList()
{
    $result = getProjets();

    if (!$result) {
        require 'views/error.php';
        exit;
    }

    $projets = pg_fetch_all($result);

    if (!$projets) {
        $noRessource = "projets";
        require 'views/noRessource.php';
        exit;
    } else {
        require 'views/projetList.php';
    }
}
function releaseList(string $nomProjet)
{
    $result = getReleases($nomProjet);

    if (!$result) {
        require 'views/error.php';
        exit;
    }

    $releases = pg_fetch_all($result);

    if (!$releases) {
        $noRessource = "releases";
        require 'views/noRessource.php';
        exit;
    } else {
        require 'views/releaseList.php';
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

