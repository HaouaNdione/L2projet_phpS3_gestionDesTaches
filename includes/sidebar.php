        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Principal</div>
                        <a class="nav-link" href="index.php">
                            <div class="sb-sidenav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Tableau de bord
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">Gestion</div>
                        
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a class="nav-link" href="indexUser.php">
                            <div class="sb-sidenav-link-icon"><i class="fas fa-users"></i></div>
                            Utilisateurs
                        </a>
                        <?php endif; ?>
                        
                        <a class="nav-link" href="indexTache.php">
                            <div class="sb-sidenav-link-icon"><i class="fas fa-tasks"></i></div>
                            Tâches
                        </a>
                        
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <div class="sb-sidenav-menu-heading">Administration</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-sidenav-link-icon"><i class="fas fa-columns"></i></div>
                            Options
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="#!">Paramètres système</a>
                                <a class="nav-link" href="#!">Logs d'activité</a>
                            </nav>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Connecté en tant que:</div>
                    <?= htmlspecialchars($_SESSION['user_prenom'] ?? '') ?> 
                    <?= htmlspecialchars($_SESSION['user_nom'] ?? '') ?>
                    <div class="small text-muted">
                        <i class="fas fa-circle text-success"></i> 
                        <?= isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin' ? 'Administrateur' : 'Utilisateur simple' ?>
                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
