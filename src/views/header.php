<header class="site-header">
    <div class="site-identity">
        <a href="?action=projetList"><img src="../bdrGirl.jpg" alt="BDR-AnimeGirl"/></a>
        <h1><a href="?action=projetList">Gestion de Projet</a></h1>
    </div>
    <nav class="site-navigation">
        <ul class="nav">
            <?php if (isset($_SESSION['userid'])) : ?>
                <li><a href="?action=projetList">Projets</a></li>
                <li><a href="?action=userList">Personnel</a></li>
                <li><a href="?action=logout">Se Déconnecter</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>