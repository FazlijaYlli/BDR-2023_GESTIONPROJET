<?php
/**
 * This script is the main entry point of the project management application.
 * It starts a PHP session, sets a debug flag and requires two files, model.php and controller.php.
 * After the required files are loaded, the script displays a header.php file.
 * If the user is not logged in, the login() function is called.
 * If the user is already logged in, the script checks the action requested by the user via the GET parameter "action".
 * If the action is not specified, the default action is set to "projetList".
 * Depending on the value of the "action" parameter, the appropriate function is called from the controller.php file.
 * Finally, the script requires a footer.php file.
 */

session_start();

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
?>
<div class="content-wrapper">
<?php

if (!isset($_SESSION['userid'])) {
    login();
}else{
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    } else {
        $action = 'projetList';
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
            task();
            break;
        case 'newprojet':
            newprojet();
            break;
        case 'newrelease':
            newrelease();
            break;
        case 'closerelease':
            closeRelease();
            break;
        case 'addUser' :
            addUser();
            break;
        case 'newTache':
            newTask();
            break;
        case 'actionTache':
            actionTask();
            break;
        case 'userList':
            userList();
            break;
        case 'userInfo':
            userInfo();
            break;
        case 'addHoliday':
            addHoliday();
            break;
        case 'comment':
            comment();
            break;
        case 'addRequirement':
            addRequirement();
            break;
        default:
            projetList();
            break;
    }
}

?>
</div>
<?php

require 'views/footer.php';