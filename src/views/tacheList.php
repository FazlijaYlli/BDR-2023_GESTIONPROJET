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