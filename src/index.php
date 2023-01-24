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

if (!isset($_SESSION['user']) and !$debug) {
    login();
}else{
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    } else {
        $action = 'home';
    }

    switch($action){
        case 'custom';
            cumstom();
            break;
        case 'home':
            //continue
        case 'projet':
            projet();
        default:
            home();
    }
}

require 'views/footer.php';