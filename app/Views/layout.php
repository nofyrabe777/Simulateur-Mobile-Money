<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulation Mobile Money</title>
    
    <link rel="stylesheet" href="<?= base_url('css/bootstrap.min.css') ?>">
    
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; letter-spacing: 1px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('client/dashboard') ?>">Simulation Mobile Money</a>
        <div class="ms-auto">
            <?php if (session()->get('client_session')): ?>
                <span class="text-white me-3"> <?= session()->get('client_session')['telephone'] ?></span>
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-danger btn-sm">Déconnexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</div>

<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>