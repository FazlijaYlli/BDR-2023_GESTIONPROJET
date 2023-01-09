<?php
/**
 * User:
 * Date:
 */

$debug = true;

session_start();

require "controller.php";?>
    <!DOCTYPE html>
    <html>
    <head>
        <link rel="stylesheet" href="style.css">
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
        default:
            home();
    }
}

require 'views/footer.php';