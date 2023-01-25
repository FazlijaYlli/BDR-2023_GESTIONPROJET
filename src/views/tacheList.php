<table>
    <!--<caption>Liste des tâches</caption>-->
    <thead>
        <th>Tâche</th>
        <th>Délai</th>
        <th>Status</th>
        <th>Action</th>
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
        <td>
            <?php if($tache['statut'] != 'Terminé') { ?>
            <form action="?action=actionTache" method="post">
                <input name="projet" type="hidden" value="<?=$tache['nomprojet'] ?>">
                <input name="release" type="hidden" value="<?=$tache['nomprojetrelease']?>">
                <input name="idTache" type="hidden" value="<?= $tache['id'] ?>">

                <?php if ($tache['statut'] == 'Planifié') { ?>
                <input type="hidden" name="type" value="assigner">
                    <input type="submit" value="S'assigner">
                <?php } else if ($tache['statut'] == 'En cours') { ?>
                <input type="hidden" name="type" value="terminer">
                    <input type="submit" value="Terminer">
                <?php } ?>

            </form>
            <?php } else { ?>
            <span>X</span>
            <?php } ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>