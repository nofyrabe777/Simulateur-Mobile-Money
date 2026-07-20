<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center" style="min-height: 75vh;">
    <div class="col-md-8 text-center">
        <h1 class="mb-5 fw-bold text-dark">Bienvenue sur Simulation Mobile Money</h1>
        
        <div class="row g-4">
            <!-- Carte Côté Client -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0 bg-white p-4">
                    <div class="card-body d-flex flex-column align-items-center">
                        <div class="fs-1 mb-3"></div>
                        <h3 class="card-title fw-bold">Espace Client</h3>
                        <p class="text-muted flex-grow-1">Consultez votre solde, effectuez des dépôts, retraits et transferts en toute simplicité.</p>
                        <!-- Redirige vers l'action de connexion ou dashboard sécurisé -->
                        <a href="<?= base_url('client/dashboard') ?>" class="btn btn-primary btn-lg w-100 mt-3">Accéder à l'Espace Client</a>
                    </div>
                </div>
            </div>

            <!-- Carte Côté Opérateur -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0 bg-white p-4">
                    <div class="card-body d-flex flex-column align-items-center">
                        <div class="fs-1 mb-3"></div>
                        <h3 class="card-title fw-bold">Espace Opérateur</h3>
                        <p class="text-muted flex-grow-1">Gérez les configurations des préfixes, ajustez les barèmes de frais et suivez la situation globale.</p>
                        <a href="<?= base_url('operateur/dashboard') ?>" class="btn btn-warning btn-lg w-100 mt-3">Accéder au Back-Office</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>