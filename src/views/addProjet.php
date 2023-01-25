<h3>Nouveau Projet</h3>
<form action="?action=newprojet" method="post">
    <label for="nameP">Nom</label><br>
    <input type="text" id="nameP" name="nameP"><br>
    <label for="responsable">1er Responsable</label><br>
    <select name="responsable" id="responsable">
        <option value="-1" disabled selected>Sélectionner</option>
        <?php foreach ($users as $user): ?>
            <option value="<?=$user['id']?>"><?= $user['prénom'] . ' ' . $user['nom']?></option>
        <?php endforeach; ?>
    </select><br>
    <label for="descriptionP">Description</label><br>
    <textarea id="descriptionP" name="descriptionP" rows="4" cols="50"></textarea><br>
    <input type="submit" value="Créer">
</form>