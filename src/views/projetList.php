<?php foreach ($projets as $projet): ?>
    <h1><a href="?action=projet&nom=<?=$projet['nom']?>"><?=$projet['nom']?></a></h1>
<?php endforeach; ?>
