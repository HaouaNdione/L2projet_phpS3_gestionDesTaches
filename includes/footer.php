            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Gestion Utilisateurs 2026</div>
                        <div>
                            <a href="#!">Politique de confidentialité</a>
                            &middot;
                            <a href="#!">Conditions d'utilisation</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/scripts.js"></script>
    <?php if (isset($include_datatables) && $include_datatables): ?>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script>
        // Initialiser DataTables
        window.addEventListener('DOMContentLoaded', event => {
            const datatablesSimple = document.getElementById('datatablesSimple');
            if (datatablesSimple) {
                new simpleDatatables.DataTable(datatablesSimple, {
                    labels: {
                        placeholder: "Rechercher...",
                        perPage: "{select} entrées par page",
                        noRows: "Aucune donnée disponible",
                        info: "Affichage de {start} à {end} sur {rows} entrées",
                    }
                });
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
