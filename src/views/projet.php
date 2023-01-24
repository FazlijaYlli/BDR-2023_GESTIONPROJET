<h1><?=$projet['nom']?></h1>



<?php
// Requête pour récupérer les releases du projet
/**$query = "SELECT nom, sortiePrévue, sortieEffective FROM projetrelease WHERE nomprojet='$nomProjet'";
$result = pg_query($db, $query);

if (!$result) {
    echo "Une erreur est survenue lors de la récupération des releases du projet.\n";
    exit;
}

// Récupère les releases du projet
$releases = pg_fetch_all($result);


if (!$result) {
    echo "Une erreur est survenue lors de la récupération des tâches du projet.\n";
    exit;
}

// Récupère les tâches du projet
$tâches = pg_fetch_all($result);

// Fermeture de la connection à la base de données
pg_close($db);
 **/
