<?php foreach ($projets as $projet): ?>
    <h1><a href="?action=projet&projet=<?=$projet['nom']?>"><?=$projet['nom']?></a></h1>
<?php endforeach; ?>
<?php if ($_SESSION['admin']): ?>
    <h3>Nouveau Projet</h3>
    <form action="?action=newprojet" method="post">
        <label for="nameP">Nom</label><br>
        <input type="text" id="nameP" name="nameP"><br>
        <label for="descriptionP">Description</label><br>
        <textarea id="descriptionP" name="descriptionP" rows="4" cols="50"></textarea><br>
        <input type="submit" value="Créer">
    </form>
<?php endif; ?>