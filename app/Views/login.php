
<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0">Accéder à votre compte</h4>
                <small class="text-white-50">Saisissez votre numéro de téléphone</small>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('auth/login') ?>" method="POST">
                    <div class="mb-3">
                        <label for="tel" class="form-label">Numéro de téléphone</label>
                        <input type="text" class="form-control form-control-lg" id="tel" name="tel" placeholder="Ex: 033/037 XX XXX XX" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>