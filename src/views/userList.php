<table>
    <tr>
        <th>Utilisateur</th>
        <th>Rôle</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <?php if ($user['responsabilité']): ?>
            <tr>
                <td><u><a href="?action=userInfo&id=<?= $user['id']?>"><?= $user['prénom'] . ' ' . $user['nom']?></a></u></td>
                <td><?=$user['responsabilité']?></td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>
