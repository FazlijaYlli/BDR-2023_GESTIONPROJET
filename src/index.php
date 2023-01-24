<?php
session_start();

require_once "model.php";

require_once "controller.php";

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
        case 'projet':
            projet();
        default:
            home();
    }
}

require 'views/footer.php';