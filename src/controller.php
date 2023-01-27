<?php

$msgTodisplay = '';
$displayError = false;

function home(){
    require 'views/home.php';
}

function projet(){
    $result = getProjetInfo($_GET['projet']);

    if (!$result) {
        echo "Une erreur est survenue lors de la récupération des informations du projet.\n";
        exit;
    }

    // Récupère les informations du projet
    $projet = pg_fetch_assoc($result);

    require 'views/projet.php';

    listUser($_GET['projet']);

    releaseList($_GET['projet']);
}


function projetList()
{
    $result = getProjets($_SESSION['userid']);
    $projets = pg_fetch_all($result);

    if (!$projets) {
        $noRessource = "projets";
        require 'views/noRessource.php';
        exit;
    } else {
        require 'views/projetList.php';
    }

    if($_SESSION['admin']){
        $resultUser = getUsers();
        $users = pg_fetch_all($resultUser);
        require 'views/addProjet.php';
    }
}

function releaseList(string $nomProjet)
{
    $result = getReleases($nomProjet);

    if (!$result) {
        require 'views/error.php';
        exit;
    }

    $roleQuery = getUserRole($_SESSION['userid'], $_GET['projet']);
    if(!$roleQuery) {
        require 'views/error.php';
        exit;
    }

    $roleFetch = pg_fetch_assoc($roleQuery);

    if(!$roleFetch) {
        $noRessource = "utilisateurs";
        require 'views/noRessource.php';
        exit;
    }

    $responsable = $roleFetch['responsabilité'] == "Responsable";

    if ($responsable) {
        include_once "views/formRelease.php";
    }

    $releases = pg_fetch_all($result);

    if (!$releases) {
        $noRessource = "releases";
        require 'views/noRessource.php';
        exit;
    } else {
        require 'views/releaseList.php';
    }
}

function listUser(String $projet)
{
    $result = getUsersRoleForProjet($projet);

    if (!$result) {
        require 'views/error.php';
        return false;
    }

    $users = pg_fetch_all($result);
    require 'views/userList.php';

    if($_SESSION['admin']){
        require 'views/addUser.php';
    }
}

function tacheList(string $projet, string $release){
    $result = getTaches($projet, $release);

    if (!$result) {
        require 'views/error.php';
        return false;
    }

    $taches = pg_fetch_all($result);

    if (!$taches) {
        $noRessource = "tâches";
        require 'views/noRessource.php';
    } else {
        require 'views/tacheList.php';
    }
}
function release(): void
{
    $result = getReleaseInfo($_GET['projet'],$_GET['release']);

    if (!$result) {
        require 'views/error.php';
        exit;
    }

    $release = pg_fetch_assoc($result);

    if (!$release) {
        require 'views/unknown.php';
        exit;
    }

    $roleQuery = getUserRole($_SESSION['userid'], $_GET['projet']);
    if(!$roleQuery) {
        require 'views/error.php';
        exit;
    }

    $roleFetch = pg_fetch_assoc($roleQuery);

    if(!$roleFetch) {
        $noRessource = "utilisateurs";
        require 'views/noRessource.php';
        exit;
    }
    $responsable = $roleFetch['responsabilité'] == "Responsable";

    require 'views/release.php';

    if($responsable) {
        $groupesTache = pg_fetch_all(getGroupesTache());

        if (!$groupesTache) {
            require 'views/error.php';
            exit;
        }

        require 'views/addTache.php';
    }

    tacheList($_GET['projet'],$_GET['release']);

    $result = getUsersRoleForProjet($release['nomprojet']);

    if (!$result) {
        require 'views/error.php';
        exit;
    }
}

function tache(): void
{
    $result = getTacheInfo($_GET['id']);

    if (!$result) {
        require 'views/error.php';
        exit;
    }

    $tache = pg_fetch_assoc($result);

    if (!$tache) {
        require 'views/unknown.php';
        return;
    }

    $result = getComments($_GET['id']);
    $comments = pg_fetch_all($result);

    $result = getRequiredTask($_GET['id']);
    $required = pg_fetch_all($result);

    require 'views/tache.php';
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
    if(isset($_POST['usr'])&&isset($_POST['psw'])){
        trylogin();
    }else{
        displayLoginPage();
    }
}

function logout(){
    unset($_SESSION['userid']);
    header('Location: ?action=login');
}


function displayLoginPage(){
    require 'views/login.php';
}

function tryLogin(){
    $res = checkPassword($_POST['usr'], $_POST['psw']);
    if(!$res){
        echo "Mauvais identifient ou mot de passe";
        unset($_POST['usr']);
        unset($_POST['psw']);
        login();
    }else{
        $_SESSION['userid'] = $res['id'];
        $_SESSION['admin'] = $res['fonction'] == 'Directeur';
        echo "Vous êtes connecté";
        header('Location: ?action=projetList');
    }
}

function checkPassword($username, $password)
{
    $result = getUserWithCredential($username, $password);
    $user = pg_fetch_assoc($result);
    return $user;
}

function newprojet(){
    if($_POST['responsable'] != -1){
        createProjet($_POST['nameP'],$_POST['descriptionP'],$_POST['responsable']);
    }
    header('Location: ?action=projetList');
}

function newrelease(){
    $date = $_POST['estimatedDate'];
    createRelease($_GET['projet'],$_POST['nameR'],$date);
    header('Location: ?action=projet&projet='.$_GET['projet']);
}
function addUser(){
    addToProject($_POST['projetname'],$_POST['userIdToAdd'],$_POST['role']);
    header('Location: ?action=projet&projet='.$_POST['projetname']);
}
function newTache(){
    createTache($_POST['titre'],$_POST['description'],$_POST['delai'],$_POST['dureeestimée'],$_POST['groupeTache'],$_POST['projet'],$_POST['release']);
    header('Location: ?action=release&projet='.$_POST['projet'].'&release='.$_POST['release']);
}

function actionTache(){
    updateTacheStatus($_POST['type'],$_POST['idTache'],$_SESSION['userid']);
    header('Location: ?action=release&projet='.$_POST['projet'].'&release='.$_POST['release']);
}

function userList()
{
    $resultUser = getUsers();
    $users = pg_fetch_all($resultUser);
    require 'views/listUser.php';
}

function userInfo()
{
    $result = getUserById($_GET['id']);
    $userInfo = pg_fetch_assoc($result);

    $result = getUserHoliday($_GET['id']);
    $userHolidays = pg_fetch_all($result);

    require 'views/userInfo.php';
}

function dateToFrench($date, $format)
{
    $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
    $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
    return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date) ) ) );
}

function addHoliday()
{
    createHoliday($_POST["debut"], $_POST["fin"], $_GET["id"]);
    header('Location: ?action=userInfo&id=' . $_GET["id"]);
}
function closeRelease() {
    terminateRelease();
    header('Location: ?action=release&projet='.$_GET['projet'].'&release='.$_GET['release']);
}

function comment()
{
    addComment($_POST['idTache'], $_POST["comment"],$_SESSION['userid']);
    header('Location: ?action=tache&id='.$_POST['idTache']);
}

