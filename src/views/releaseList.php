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