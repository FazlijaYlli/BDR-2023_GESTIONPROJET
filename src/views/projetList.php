<?php foreach ($projets as $projet): ?>
    <h1><a href="?action=projet&projet=<?=$projet['nom']?>"><?=$projet['nom']?></a></h1>
<?php endforeach; ?>
