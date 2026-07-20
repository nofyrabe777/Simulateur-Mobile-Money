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
        <!-- Détection si l'URL courante correspond à la zone Opérateur -->
        <?php if (strpos(current_url(), 'operateur') !== false): ?>
            <!-- Affichage spécifique pour l'espace Opérateur -->
            <a class="navbar-brand text-warning" href="<?= base_url('operateur/dashboard') ?>"> Administration Opérateur</a>
            <div class="ms-auto">
                <a href="<?= base_url('/') ?>" class="btn btn-outline-light btn-sm">Quitter l'espace Admin</a>
            </div>
        <?php else: ?>
            <!-- Affichage classique pour l'espace Client -->
            <a class="navbar-brand" href="#">Simulation Mobile Money</a>
            <div class="ms-auto">
                <?php if (session()->get('client_session')): ?>
                    <?php $sessionUser = session()->get('client_session'); ?>
                    <!-- Gestion adaptative selon le nom de votre colonne en BDD (tel ou telephone) -->
                    <span class="text-white me-3"> <?= $sessionUser['tel'] ?? $sessionUser['telephone'] ?? '' ?></span>
                    <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-danger btn-sm">Déconnexion</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <!-- Messages flash de succès -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Messages flash d'erreur -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Injection du contenu des vues enfants -->
    <?= $this->renderSection('content') ?>
</div>

<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>