<?php
/**
 * User:
 * Date:
 */
session_start();

require "controller.php";

require 'views/header.php';

if (isset($_SESSION['user'])) {
    login();
}else{
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    } else {
        $action = 'home';
    }

    switch($action){
        //TODO
        case 'home':
            //continue
        default:
            home();
    }
}

require 'views/footer.php';