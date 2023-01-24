<?php

$msgTodisplay = '';
$displayError = false;

function home(){
    require 'views/home.php';
}

function projet(){
    require 'views/projet.php';
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

