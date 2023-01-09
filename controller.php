<?php

function home(){
    require 'views/home.php';
}

function login()
{
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
    if (password_verify($_POST['username'], $_POST['password'])) {
        $_SESSION['user'] =  $_POST['username'];
        redirect("home");
    } else {
        alert('Identifiants incorrects ...');
        displayLoginPage();
    }
}

function alert($msg){
    $_SESSION['msg'] = $msg;
    //TODO
}

