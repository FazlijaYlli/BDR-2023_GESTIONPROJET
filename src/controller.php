<?php

/**
 * Main script for managing projects and its related resources (releases, tasks, and users).
 */

$msgTodisplay = '';
$displayError = false;

/**
 * Displays a project page.
 * The project information is retrieved using `getProjetInfo` function.
 * If an error occurs during the retrieval of project information, an error message is displayed.
 *
 * The `views/projet.php` is then included to display the project information.
 *
 * The functions `listUser` and `releaseList` are then called with the project name as argument.
 */
function projet(){
    $result = getProjetInfo($_GET['projet']);

    if (!$result) {
        echo "Une erreur est survenue lors de la récupération des informations du projet.\n";
        return;
    }

    // Récupère les informations du projet
    $projet = pg_fetch_assoc($result);

    require 'views/projet.php';

    listUser($_GET['projet']);


    releaseList($_GET['projet']);
}


/**
 * Displays the list of projects.
 * The list of projects is retrieved using `getProjets` function with the user id as argument.
 * If the result of the function is false, it means no project is available for the user.
 * In this case, the script includes the `views/noRessource.php` file to display an appropriate message.
 *
 * If the user is an administrator, the function `addProjet` is also included.
 */
function projetList()
{
    $result = getProjets($_SESSION['userid']);
    $projets = pg_fetch_all($result);

    if (!$projets) {
        $noRessource = "projets";
        require 'views/noRessource.php';
        return;
    } else {
        require 'views/projetList.php';
    }

    if($_SESSION['admin']){
        $resultUser = getUsers();
        $users = pg_fetch_all($resultUser);
        require 'views/addProjet.php';
    }
}

/**
 * Function to display the release list for a given project.
 *
 * This function first retrieves the releases for the project using the `getReleases` function.
 * If no releases are found, an error view is displayed.
 * The user's role in the project is then retrieved using the `getUserRole` function.
 * If the user has no role in the project, an error view is displayed.
 * The user's responsibility is then checked and if the user is the responsible, a form for adding releases is displayed.
 * If there are releases for the project, the `releaseList` view is displayed.
 *
 * @param string $nomProjet The name of the project to display releases for.
 *
 * @return void
 */
function releaseList(string $nomProjet)
{
    $result = getReleases($nomProjet);

    if (!$result) {
        require 'views/error.php';
        return;
    }

    $roleQuery = getUserRole($_SESSION['userid'], $_GET['projet']);
    if(!$roleQuery) {
        require 'views/error.php';
        return;
    }

    $roleFetch = pg_fetch_assoc($roleQuery);

    if(!$roleFetch) {
        echo "Aucun droit sur ce projet";
        return;
    }

    $responsable = $roleFetch['responsabilité'] == "Responsable";

    if ($responsable) {
        include_once "views/formRelease.php";
    }

    $releases = pg_fetch_all($result);

    if (!$releases) {
        $noRessource = "releases";
        require 'views/noRessource.php';
        return;
    } else {
        require 'views/releaseList.php';
    }
}

/**
 * function listUser - Retrieves and displays a list of users with their roles in a project
 *
 * @param string $projet The name of the project to retrieve the users and their roles for
 *
 * @return bool False if the retrieval of the users and their roles failed, otherwise void
 */

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

/**
 * Task list function to retrieve and display tasks for a project and release.
 *
 * @param string $projet The name of the project to retrieve tasks for.
 * @param string $release The name of the release to retrieve tasks for.
 */
function taskList(string $projet, string $release){
    $result = getTasks($projet, $release);

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

/**
 * This function retrieves information about a release and its associated tasks, and shows the result to the user.
 *
 * It first gets the release information using the `getReleaseInfo` function and stores the result in the `$release` variable.
 * If the function returns false, or if the result is empty, an error message is displayed.
 *
 * Then, it gets the role of the current user in the project using the `getUserRole` function and stores the result in the `$roleFetch` variable.
 * If the function returns false, or if the result is empty, a message is displayed indicating that there are no users in the project.
 *
 * The `release.php` view is then included, and if the user is a project responsible, the `addTache.php` view is included as well.
 *
 * Finally, the `taskList` function is called to display the list of tasks associated with the release.
 */

function release(): void
{
    $result = getReleaseInfo($_GET['projet'],$_GET['release']);

    if (!$result) {
        require 'views/error.php';
        return;
    }

    $release = pg_fetch_assoc($result);

    if (!$release) {
        require 'views/unknown.php';
        return;
    }

    $roleQuery = getUserRole($_SESSION['userid'], $_GET['projet']);
    if(!$roleQuery) {
        require 'views/error.php';
        return;
    }

    $roleFetch = pg_fetch_assoc($roleQuery);

    if(!$roleFetch) {
        $noRessource = "utilisateurs";
        require 'views/noRessource.php';
        return;
    }
    $responsable = $roleFetch['responsabilité'] == "Responsable";

    require 'views/release.php';

    if($responsable) {
        $groupesTache = pg_fetch_all(getGroupesTache());

        if (!$groupesTache) {
            require 'views/error.php';
            return;
        }

        require 'views/addTache.php';
    }

    taskList($_GET['projet'],$_GET['release']);

    $result = getUsersRoleForProjet($release['nomprojet']);

    if (!$result) {
        require 'views/error.php';
        return;
    }
}

/**
 * This function retrieves information about a specific task and shows it to the user.
 *
 * It first gets the task information using the `getTacheInfo` function and stores the result in the `$tache` variable.
 * If the function returns false, or if the result is empty, an error message is displayed.
 *
 * Then, it retrieves all comments associated with the task using the `getComments` function and stores the result in the `$comments` variable.
 * It also retrieves all tasks that are required for the completion of the task, using the `getRequiredTask` function, and stores the result in the `$required` variable.
 * Finally, it retrieves all incompleted tasks related to the task using the `getOtherImcompletTask` function and stores the result in the `$getOtherImcompletTask` variable.
 *
 * The `tache.php` view is then included, displaying the information about the task to the user.
 */

function task(): void
{
    $result = getTacheInfo($_GET['id']);

    if (!$result) {
        require 'views/error.php';
        return;
    }

    $tache = pg_fetch_assoc($result);

    if (!$tache) {
        require 'views/unknown.php';
        return;
    }

    $result = getComments($_GET['id']);
    $comments = pg_fetch_all($result);

    $result = getOtherImcompletTask($_GET['id']);
    $getOtherImcompletTask = pg_fetch_all($result);

    $result = getRequiredTask($_GET['id']);
    $required = pg_fetch_all($result);

    require 'views/tache.php';
}

/**
 * This function handles the login process for the user.
 *
 * If the `usr` and `psw` POST parameters are set, the `tryLogin` function is called to attempt to log the user in.
 * If the `usr` and `psw` POST parameters are not set, the `displayLoginPage` function is called to show the login page to the user.
 */

function login(){
    if(isset($_POST['usr'])&&isset($_POST['psw'])){
        trylogin();
    }else{
        displayLoginPage();
    }
}

/**
 * This function logs out the current user by unsetting their user ID from the session and redirecting to the login page.
 */
function logout(){
    unset($_SESSION['userid']);
    header('Location: ?action=login');
}

/**
 * This function displays the login page to the user.
 *
 * It includes the `login.php` view.
 */
function displayLoginPage(){
    require 'views/login.php';
}

/**
 * This function attempts to log in the user with the given username and password.
 *
 * First, it calls the `checkPassword` function to verify the provided credentials.
 * If the function returns false, an error message is displayed, and the login form is redisplayed.
 *
 * If the credentials are correct, the user's ID and admin status are stored in the session, and a success message is displayed.
 * The user is then redirected to the project list page.
 */
function tryLogin(){
    $res = checkPassword($_POST['usr'], $_POST['psw']);
    if(!$res){
        echo "Mauvais identifiant ou mot de passe";
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

/**
 * This function checks the provided username and password against the database, and returns the user data if the credentials are valid.
 *
 * It first calls the `getUserWithCredential` function to retrieve the user information from the database, using the `$username` and `$password` parameters as input.
 * The result is stored in the `$result` variable.
 *
 * Then, it uses the `pg_fetch_assoc` function to fetch the user data as an associative array, and stores the result in the `$user` variable.
 *
 * Finally, the `$user` variable is returned, containing the user data if the credentials are valid, or `false` otherwise.
 */
function checkPassword($username, $password)
{
    $result = getUserWithCredential($username, $password);
    $user = pg_fetch_assoc($result);
    return $user;
}

/**
 * This function creates a new project.
 *
 * It checks if a responsible has been selected in the form (`$_POST['responsable'] != -1`),
 * and if so, calls the `createProjet` function with the project name, description, and responsible.
 *
 * Then, it redirects the user to the project list page.
 */
function newprojet(){
    if($_POST['responsable'] != -1){
        createProjet($_POST['nameP'],$_POST['descriptionP'],$_POST['responsable']);
    }
    header('Location: ?action=projetList');
}
/**
 * This function creates a new release for a project.
 *
 * It calls the `createRelease` function with the project id, release name, and estimated date.
 * Then, it redirects the user to the project page.
 */
function newrelease(){
    $date = $_POST['estimatedDate'];
    createRelease($_GET['projet'],$_POST['nameR'],$date);
    header('Location: ?action=projet&projet='.$_GET['projet']);
}

/**
 * This function adds a user to a project.
 *
 * It calls the `addToProject` function with the project name, user id, and role.
 * Then, it redirects the user to the project page.
 */
function addUser(){
    addToProject($_POST['projetname'],$_POST['userIdToAdd'],$_POST['role']);
    header('Location: ?action=projet&projet='.$_POST['projetname']);
}

/**
 * This function creates a new task for a release.
 *
 * It calls the `createTache` function with the task title, description, deadline, estimated duration, task group, project id, and release id.
 * Then, it redirects the user to the release page.
 */
function newTask(){
    createTache($_POST['titre'],$_POST['description'],$_POST['delai'],$_POST['dureeestimée'],$_POST['groupeTache'],$_POST['projet'],$_POST['release']);
    header('Location: ?action=release&projet='.$_POST['projet'].'&release='.$_POST['release']);
}

/**
 * This function updates the status of a task.
 *
 * It calls the `updateTacheStatus` function with the type of action, task id, and user id.
 * Then, it redirects the user to the release page.
 */
function actionTask(){
    updateTacheStatus($_POST['type'],$_POST['idTache'],$_SESSION['userid']);
    header('Location: ?action=release&projet='.$_POST['projet'].'&release='.$_POST['release']);
}
/**
 * This function lists the users.
 *
 * It calls the `getUsers` function to retrieve all users, then assigns the result to the `$users` variable.
 * Finally, it includes the `views/listUser.php` file.
 */
function userList()
{
    $resultUser = getUsers();
    $users = pg_fetch_all($resultUser);
    require 'views/listUser.php';
}

/**
 * This function displays user information.
 *
 * It calls the `getUserById` function with the `$_GET['id']` value to retrieve the information of a user and assigns the result to the `$userInfo` variable.
 * It also calls the `getUserHoliday` function with the `$_GET['id']` value to retrieve the holiday information of the user and assigns the result to the `$userHolidays` variable.
 * Finally, it includes the `views/userInfo.php` file.
 */
function userInfo()
{
    $result = getUserById($_GET['id']);
    $userInfo = pg_fetch_assoc($result);

    $result = getUserHoliday($_GET['id']);
    $userHolidays = pg_fetch_all($result);

    require 'views/userInfo.php';
}

/**
 * This function converts a date from English to French format.
 *
 * It takes a date and a format as input, replaces the English days and months with their French counterparts, and returns the result.
 *
 * @param string $date The date to convert.
 * @param string $format The desired format of the converted date.
 * @return string The date in French format.
 */
function dateToFrench($date, $format)
{
    $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
    $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
    return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date) ) ) );
}

/**
 * This function creates a new holiday for a user.
 *
 * It calls the `createHoliday` function with the start date, end date, and user id as parameters.
 * Then, it redirects the user to the user information page.
 */
function addHoliday()
{
    createHoliday($_POST["debut"], $_POST["fin"], $_GET["id"]);
    header('Location: ?action=userInfo&id=' . $_GET["id"]);
}

/**
 * Closes a release by calling the `terminateRelease` function.
 *
 * After the release is terminated, the user is redirected to the release information page.
 */
function closeRelease() {
    terminateRelease();
    header('Location: ?action=release&projet='.$_GET['projet'].'&release='.$_GET['release']);
}

/**
 * Adds a comment to a task by calling the `addComment` function.
 *
 * The `idTache`, comment text, and user id are passed as parameters to the function.
 * After the comment is added, the user is redirected to the task information page.
 */
function comment()
{
    addComment($_POST['idTache'], $_POST["comment"],$_SESSION['userid']);
    header('Location: ?action=tache&id='.$_POST['idTache']);
}

/**
 * Adds a task requirement to a task by calling the `addRequireTask` function.
 *
 * The `idTache` and the required task are passed as parameters to the function.
 * After the required task is added, the user is redirected to the task information page.
 */
function addRequirement()
{
    addRequireTask($_POST['idTache'], $_POST["requiredtoadd"]);
    header('Location: ?action=tache&id='.$_POST['idTache']);
}

