<?php foreach ($projets as $projet): ?>
    <h2><u><a href="?action=projet&projet=<?=$projet['nom']?>"><?=$projet['nom']?></a></u></h2>
<?php endforeach; ?>