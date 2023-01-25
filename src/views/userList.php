<table>
    <tr>
        <th>Utilisateur</th>
        <th>Rôle</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <?php if ($user['responsabilité']): ?>
            <tr>
                <td><?= $user['prénom'] . ' ' . $user['nom']?></td>
                <td><?=$user['responsabilité']?></td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>
