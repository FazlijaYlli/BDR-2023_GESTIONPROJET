<a href="?action=tache&id=<?=$tache['id']?>">
    <h3><?= $tache['titre'] ?></h3>
</a>
<span>Statut : <?= $tache['statut'] ?></span>

<?php if (count($required)): ?>
    <?php foreach ($required as $req): ?>
        <p><b>Tâche requise : </b><a href="http://localhost:8888/?action=tache&id=<?= $req['id']?>"><?= $req['titre']?></a> <?= $req['statut']?></p>
    <?php endforeach; ?>
<?php endif; ?>

<p>Description : <?= $tache['description'] ?></p>
<p>Délai : <?= $tache['delai'] ?></p>
<p>Durée estimée : <?= $tache['dureeestimée'] ?></p>
<p>Durée réelle : <?= $tache['dureeréelle'] ?? 'Non terminée'?></p>
<p>
    Projet :
    <a href="?action=projet&projet=<?=$tache['nomprojet']?>">
        <u><?= $tache['nomprojet'] ?></u>
    </a>
</p>
<p>
    Release :
    <a href="?action=release&projet=<?=$tache['nomprojet']?>&release=<?=$tache['nomprojetrelease']?>">
        <u><?= $tache['nomprojetrelease'] ?></u>
    </a>
</p>

<?php if (count($comments)): ?>
    <h4>Commentaire(s)</h4>
    <table>
        <thead>
            <th>Date</th>
            <th>Utilisateur</th>
            <th></th>
        </thead>
    <?php foreach ($comments as $comment): ?>
        <tr>
            <td><?= $comment['date']?></td>
            <td><?= $comment['prénom'].' '.$comment['nom']?></td>
            <td><?= $comment['comment']?></td>
        </tr>
    <?php endforeach; ?>
    </table>
<?php endif; ?>
<br>
<form action="?action=comment" method="post">
    <label for="comment">Ajouter un commentaire</label><br>

    <textarea id="comment" name="comment" rows="4" cols="50"></textarea><br>
    <input name="idTache" type="hidden" value="<?= $tache['id'] ?>">
    <input type="submit" value="Envoyer">
</form>
