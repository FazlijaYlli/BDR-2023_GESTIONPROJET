
<?php if ($_SESSION['userid']): ?>
    <h3>Nouvelle Tache</h3>
    <form action="?action=newtache" method="post">
        <label for="titre">titre</label><br>
        <input type="text" id="titre" name="titre"><br>

        <label for="description">Description</label><br>
        <textarea id="description" name="description" rows="4" cols="50"></textarea><br>

        <label for="delai">Delai</label><br>
        <input type="date" id="delai" name="delai"><br>

        <label for="dureeestimée">Durée estimée</label><br>
        <input type="time" id="dureeestimée" name="dureeestimée" min="00:05:00" max="24:00:00"><br>

        <input type="submit" value="Créer">
    </form>
<?php endif; ?>