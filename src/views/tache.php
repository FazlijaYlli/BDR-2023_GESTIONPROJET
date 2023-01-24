<a href="?action=tache&id=<?=$tache['id']?>">
    <h3><?= $tache['titre'] ?></h3>
</a>
<span><?= $tache['statut'] ?></span>

<?php
if($TASK_DETAILS){
?>
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
<?php
}
?>