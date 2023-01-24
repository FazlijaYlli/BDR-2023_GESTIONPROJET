<?php
require_once $_SERVER['DOCUMENT_ROOT']."/setting.php";

$db = pg_connect("$host $port $dbname $credentials");