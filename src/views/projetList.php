<?php foreach ($projets as $projet): ?>
    <h2><a href="?action=projet&projet=<?=$projet['nom']?>"><?=$projet['nom']?></a></h2>
<?php endforeach; ?>
