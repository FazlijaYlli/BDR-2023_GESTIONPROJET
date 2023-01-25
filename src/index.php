<?php
session_start();

$debug = true;
require_once "model.php";

require_once "controller.php";

?>

    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <link rel="stylesheet" href="style.css">
        <title>Gestion de Projet</title>
    </head>

<?php
require 'views/header.php';


if (!isset($_SESSION['userid'])) {
    login();
}else{
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    } else {
        $action = 'home';
    }

    switch($action){
        case 'logout';
            logout();
            break;
        case 'projet':
            projet();
            break;
        case 'projetList':
            projetList();
            break;
        case 'release':
            release();
            break;
        case 'tache':
            tache();
            break;
        default:
            home();
            break;
    }
}

require 'views/footer.php';