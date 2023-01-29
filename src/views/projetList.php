<?php foreach ($projets as $projet): ?>
    <h1><u><a href="?action=projet&projet=<?=$projet['nom']?>"><?=$projet['nom']?></a></u></h1>
<?php endforeach; ?>