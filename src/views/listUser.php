<h1>Personnel</h1>
<?php foreach ($users as $user): ?>
    <p>
        <a href="?action=userInfo&id=<?= $user['id'] ?>"><?= $user['prénom'] . ' ' . $user['nom']?></a>
    </p>
<?php endforeach; ?>