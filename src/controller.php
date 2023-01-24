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
    if(isset($_POST['username'])&&isset($_POST['password'])){
        trylogin();
    }else{
        displayLoginPage();
    }
}

function displayLoginPage(){
    require 'views/login.php';
}

function tryLogin(){
    if (checkPassword($_POST['username'], $_POST['password'])) {
        $_SESSION['user'] =  $_POST['username'];
        redirect("home");
    } else {
        displayLoginPage();
    }
}

function checkPassword(mixed $username, mixed $password)
{
    return true; //TODO
}

function redirect($action, $id = 0)
{
    if ($id > 0) {
        header('Location: ?action=' . $action . '&id=' . $id);
    } else {
        header('Location: ?action=' . $action);
    }
}

