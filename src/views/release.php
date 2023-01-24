<h1><?=$release['nom']?></h1>

<a href="?action=projet&projet=<?=$release['nomprojet']?>">
    <h2><?= $release['nomprojet'] ?></h2>
</a>

<?php
echo "<p>";

echo $release['sortieprévue'];

echo "</p>";
?>

<?php
echo "<p>";

echo $release['sortieeffective'] ?? 'Pas fini';

echo "</p>";
?>
