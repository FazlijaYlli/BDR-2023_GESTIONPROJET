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
}

function projetList()
{
    $result = getProjets($_SESSION['userid']);
    $projets = pg_fetch_all($result);
    require 'views/projetList.php';
}

function listTaches(string $projet, string $release){
    $result = getListTacheInfo($projet, $release);

    if (!$result) {
        require 'views/error.php';
        return false;
    }

    return $result;
}
function releaseProjet(): void
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

    $taches = listTaches($_GET['projet'],$_GET['release']);

    if(!$taches){
        require 'views/noTaches.php';
        exit;
    }

    while ($tache = pg_fetch_assoc($taches)) {
        $TASK_DETAILS = false;
        require 'views/tache.php';
    }
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

    $TASK_DETAILS = true;
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
    creatProjet($_POST['nameP'],$_POST['descriptionP']);
    header('Location: ?action=projetList');
}

