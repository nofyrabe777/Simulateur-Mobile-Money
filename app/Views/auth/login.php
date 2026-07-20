<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="mb-3">Connexion</h3>
                <p class="text-muted">Entrez votre numéro de téléphone pour accéder à votre espace.</p>
                <form action="<?= base_url('auth/login') ?>" method="post">
                    <div class="mb-3">
                        <label for="tel" class="form-label">Numéro de téléphone</label>
                        <input type="text" class="form-control" id="tel" name="tel" placeholder="Ex: 033XXXXXXXX" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>