<h3>Ajouter un utilisateur</h3>
<form action="?action=addUser" method="post">
    <div class="container">
        <select name="userIdToAdd" id="userToAdd">
            <option value="-1" disabled selected>Sélectionner</option>
            <?php foreach ($users as $user): ?>
                <?php if (!$user['responsabilité']): ?>
                    <option value="<?=$user['id']?>"><?= $user['prénom'] . ' ' . $user['nom']?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>

        <select name="role" id="role">
            <option value="Employé">Employé</option>
            <option value="Responsable">Responsable</option>
        </select>

        <input name="projetname" type="hidden" value="<?=$projet?>">
        <div>
            <button type="submit">Ajouter</button>
        </div>
    </div>
</form>

