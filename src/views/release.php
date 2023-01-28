<h1><?=$release['nom']?></h1>

<u>
    <a href="?action=projet&projet=<?=$release['nomprojet']?>">
        <h2><?= $release['nomprojet'] ?></h2>
    </a>
</u>

<p>Sortie Prévue : <?= $release['sortieprévue']?></p>

<p>Sortie Effective : <?= $release['sortieeffective'] ?? 'Pas fini'?><p>


<?php if ($responsable && $release['sortieeffective'] == null): ?>
    <a href="?action=closerelease&projet=<?=$_GET['projet']?>&release=<?=$_GET['release']?>"><button>Fermer</button></a>
<?php endif; ?>