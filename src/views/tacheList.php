<table>
    <!--<caption>Liste des tâches</caption>-->
    <thead>
        <th>Tâche</th>
        <th>Délai</th>
        <th>Status</th>
        <th>Utilisateur</th>
        <th>Action</th>
    </thead>
<?php foreach ($taches as $tache): ?>
    <tr>
        <td>
            <u><a href="?action=tache&id=<?=$tache['id']?>"><?= $tache['titre'] ?></a></u>
        </td>
        <td>
            <?= $tache['delai']?>
        </td>
        <td>
            <q><?= $tache['statut']?></q>
        </td>
        <td>
            <?= ($tache['userprénom'] ?? 'Non assigné')." ".($tache['usernom'] ?? '')?>
        </td>
        <td>
                <?php if ($tache['statut'] == 'Planifié') { ?>
                <form action="?action=actionTache" method="post">
                    <input name="projet" type="hidden" value="<?=$tache['nomprojet'] ?>">
                    <input name="release" type="hidden" value="<?=$tache['nomprojetrelease']?>">
                    <input name="idTache" type="hidden" value="<?= $tache['id'] ?>">
                    <input type="hidden" name="type" value="assigner">
                    <input type="submit" value="S'assigner">
                </form>
                <?php } else if ($tache['statut'] == 'En cours'
                        AND $_SESSION['userid'] == $tache['idutilisateur']) { ?>
                <form action="?action=actionTache" method="post">
                    <input name="projet" type="hidden" value="<?=$tache['nomprojet'] ?>">
                    <input name="release" type="hidden" value="<?=$tache['nomprojetrelease']?>">
                    <input name="idTache" type="hidden" value="<?= $tache['id'] ?>">
                    <input type="hidden" name="type" value="terminer">
                    <input type="submit" value="Terminer">
                </form>
                <?php } else { ?>
                    <span>x</span>
                <?php } ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>