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