<?php

$msgTodisplay = '';
$displayError = false;

function home(){
    require 'views/home.php';
}

function projet(){
    $result = getProjetInfo($_GET['nom']);

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
    $result = getProjets();
    $projets = pg_fetch_all($result);
    require 'views/projetList.php';
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

