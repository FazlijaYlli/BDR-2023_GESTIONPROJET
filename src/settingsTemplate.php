<?php
// TODO : COMPLETE AND SAVE AS settings.php


$DEV = false;

// Connexion à la base de données
$host = "host=".$_ENV["DB_HOST"];
$port = "port=".$_ENV["DB_PORT"];
$dbname = "dbname=".$_ENV["DB_NAME"];
$credentials = "user=".$_ENV["DB_USER"]."password=".$_ENV["DB_PASSWORD"];

if ($DEV){
    $host = "host=";
    $port = "port=";
    $dbname = "dbname=";
    $credentials = "user= password=";
}