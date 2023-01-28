<h1><?= $userInfo['prénom'] ?> <?= $userInfo['nom'] ?></h1>

<?php if($_SESSION['admin']): ?>
    <h3>Nouveau Congé</h3>
    <form action="?action=addHoliday&id=<?= $userInfo['id'] ?>" method="post">
        <label for="debut">Début</label><br>
        <input type="date" id="debut" name="debut" value="<?php echo date('Y-m-d'); ?>"><br>
        <label for="fin">Fin</label><br>
        <input type="date" id="fin" name="fin" value="<?php echo date('Y-m-d'); ?>">
        <input type="submit" value="Ajouter">
    </form>
<?php endif; ?>

<h3>Congés</h3>
<?php if(count($userHolidays)): ?>
    <?php foreach ($userHolidays as $userHoliday): ?>
        <p>De : <?=dateToFrench($userHoliday['debut'],'l j F Y') ?> à <?=dateToFrench($userHoliday['fin'],'l j F Y') ?> (<?=$userHoliday['statut']?>)</p>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucun</p>
<?php endif; ?>




