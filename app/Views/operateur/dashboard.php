<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration Opérateur - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Espace Administration Opérateur</a>
    </div>
</nav>

<div class="container my-4">

    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body p-4 text-center">
                    <h5 class="card-title text-uppercase opacity-75">Gains Totaux (Frais perçus)</h5>
                    <h2 class="display-5 fw-bold mb-0"><?= number_format($totalGains ?? 0, 0, ',', ' ') ?> Ar</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-dark text-white shadow-sm">
                <div class="card-body p-4 text-center">
                    <h5 class="card-title text-uppercase opacity-75">Nombre Total de Clients</h5>
                    <h2 class="display-5 fw-bold mb-0"><?= $totalClients ?? 0 ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold">Ajouter un Préfixe Réseau</div>
                <div class="card-body">
                    <form action="<?= base_url('operateur/prefixe/add') ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label">Préfixe (ex: 033, 037)</label>
                            <input type="text" name="prefixe" class="form-control" maxlength="4" required placeholder="033">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Ajouter le préfixe</button>
                    </form>

                    <hr>
                    <h6>Préfixes existants :</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if (!empty($prefixes)): ?>
                            <?php foreach ($prefixes as $p): ?>
                                <span class="badge bg-secondary p-2"><?= $p['valeur'] ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted small">Aucun préfixe configuré</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold">Mettre à jour un Barème de Frais</div>
                <div class="card-body">
                    <form action="<?= base_url('operateur/bareme/update') ?>" method="post" class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Type Opération</label>
                            <select name="id_type_operation" class="form-select" required>
                                <option value="2">Retrait</option>
                                <option value="3">Transfert</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Montant Min</label>
                            <input type="number" name="montant_min" class="form-control" required placeholder="100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Montant Max</label>
                            <input type="number" name="montant_max" class="form-control" required placeholder="5000">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Frais (Ar)</label>
                            <input type="number" name="frais" class="form-control" required placeholder="200">
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-warning w-100 fw-bold">Mettre à jour le barème</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold py-3">Situation des Comptes Clients</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#ID</th>
                            <th>Téléphone</th>
                            <th>Solde Actuel</th>
                            <th>Date de création</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($comptes)): ?>
                            <?php foreach ($comptes as $c): ?>
                                <tr>
                                    <td><?= $c['id'] ?></td>
                                    <td><?= $c['telephone'] ?></td>
                                    <td class="fw-bold text-success"><?= number_format($c['solde'], 0, ',', ' ') ?> Ar</td>
                                    <td><?= $c['created_at'] ?? 'N/A' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-3">Aucun client trouvé.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>