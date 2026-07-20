<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white p-3 shadow-sm text-center">
            <h6>Gains Retraits</h6>
            <h3><?= number_format($gains_retrait, 2, ',', ' ') ?> Ar</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white p-3 shadow-sm text-center">
            <h6>Gains Transferts</h6>
            <h3><?= number_format($gains_transfert, 2, ',', ' ') ?> Ar</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-dark text-white p-3 shadow-sm text-center">
            <h6>Gains Totaux</h6>
            <h3><?= number_format($gains_totaux, 2, ',', ' ') ?> Ar</h3>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Situation des comptes clients -->
    <div class="col-md-7">
        <div class="card shadow-sm h-100"> 
            <div class="card-header bg-white py-3 fw-bold">Situation des Comptes Clients</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Téléphone</th>
                            <th>Solde actuel</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($clients as $c): ?>
                            <tr>
                                <!-- Utilisation de la clé correcte 'tel' ou 'telephone' selon votre DB -->
                                <td><?= $c['tel'] ?? $c['telephone'] ?></td>
                                <td class="fw-bold"><?= number_format($c['solde'], 2, ',', ' ') ?> Ar</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Gestion des préfixes -->
    <div class="col-md-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3 fw-bold">Configuration des Préfixes</div>
            <div class="card-body">
                <form action="<?= base_url('operateur/prefixe/add') ?>" method="POST" class="d-flex mb-3">
                    <input type="text" name="prefixe" class="form-control me-2" placeholder="Ex: 034" required maxlength="10">
                    <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
                </form>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach($prefixes as $p): ?>
                        <span class="badge bg-secondary p-2 fs-6"><?= $p['prefixe'] ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Configuration des barèmes -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3 fw-bold">Configuration des Barèmes de Frais</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Tranche Min</th>
                    <th>Tranche Max</th>
                    <th>Frais actuel</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($baremes as $b): ?>
                    <tr>
                        <form action="<?= base_url('operateur/bareme/update') ?>" method="POST">
                            <input type="hidden" name="id" value="<?= $b['id'] ?>">
                            <td><span class="badge bg-dark"><?= $b['type'] ?></span></td>
                            <td><input type="number" step="0.01" name="montant_min" class="form-control form-control-sm" value="<?= $b['montant_min'] ?>" required></td>
                            <td><input type="number" step="0.01" name="montant_max" class="form-control form-control-sm" value="<?= $b['montant_max'] ?>" required></td>
                            <td><input type="number" step="0.01" name="frais" class="form-control form-control-sm" value="<?= $b['frais'] ?>" required></td>
                            <td><button type="submit" class="btn btn-warning btn-sm">Mettre à jour</button></td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>