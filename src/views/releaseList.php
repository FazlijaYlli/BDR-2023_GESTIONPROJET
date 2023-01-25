<table>
    <!--<caption>Liste des tâches</caption>-->
    <thead>
        <th>Release</th>
        <th>Sortie Prévue</th>
        <th>Sortie Effective</th>
    </thead>
    <?php foreach ($releases as $release): ?>
        <tr>
            <td>
                <a href="?action=release&projet=<?=$release['nomprojet']?>&release=<?=$release['nom']?>"><?=$release['nom']?></a>
            </td>
            <td>
                <?= $release['sortieprévue']?>
            </td>
            <td>
                <?= $release['sortieeffective'] ?? 'Non déployée'?>
            </td>
        </tr>
    <?php endforeach; ?>

</table>
<?php if ($responsable): ?>
<h3>Nouvelle Release</h3>
<form action="?action=newrelease&projet=<?=$_GET['projet']?>" method="post">
    <label for="nameR">Nom</label><br>
    <input type="text" id="nameR" name="nameR"><br>
    <label for="estimatedDate">Date de fin estimée</label><br>
    <input type="date" id="estimatedDate" name="estimatedDate">
    <input type="submit" value="Créer">
</form>
<?php endif ?>