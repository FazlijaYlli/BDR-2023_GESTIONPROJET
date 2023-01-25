<table>
    <!--<caption>Liste des tâches</caption>-->
    <thead>
        <th>Tâche</th>
        <th>Délai</th>
        <th>Status</th>
    </thead>
<?php foreach ($taches as $tache): ?>
    <tr>
        <td>
            <a href="?action=tache&id=<?=$tache['id']?>"><?= $tache['titre'] ?></a>
        </td>
        <td>
            <?= $tache['delai']?>
        </td>
        <td>
            <q><?= $tache['statut']?></q>
        </td>
    </tr>
<?php endforeach; ?>
</table>

<?php if ($_SESSION['admin']): ?>
    <h3>Nouveau Projet</h3>
    <form action="?action=newprojet" method="post">
        <label for="nameP">Nom</label><br>
        <input type="text" id="nameP" name="nameP"><br>
        <label for="descriptionP">Description</label><br>
        <textarea id="descriptionP" name="descriptionP" rows="4" cols="50"></textarea><br>
        <input type="submit" value="Créer">
    </form>
<?php endif; ?>