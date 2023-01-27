<a href="?action=tache&id=<?=$tache['id']?>">
    <h3><?= $tache['titre'] ?></h3>
</a>
<span><?= $tache['statut'] ?></span>

<p><?= $tache['description'] ?></p>
<p><?= $tache['delai'] ?></p>
<p><?= $tache['dureeestimée'] ?></p>
<p><?= $tache['dureeréelle'] ?? 'Non terminée'?></p>
<a href="?action=projet&projet=<?=$tache['nomprojet']?>">
    <p><?= $tache['nomprojet'] ?></p>
</a>
<a href="?action=release&projet=<?=$tache['nomprojet']?>&release=<?=$tache['nomprojetrelease']?>">
    <p><?= $tache['nomprojetrelease'] ?></p>
</a>

<form action="?action=comment" method="post">
    <label for="comment">Ajouter un commentaire</label><br>

    <textarea id="comment" name="comment" rows="4" cols="50"></textarea><br>
    <input name="idTache" type="hidden" value="<?= $tache['id'] ?>">
    <input type="submit" value="Envoyer">
</form>
<?php if (count($comments)): ?>
    <table>
        <thead>
            <th>Date</th>
            <th>Utilisateur</th>
            <th>Commentaire</th>
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