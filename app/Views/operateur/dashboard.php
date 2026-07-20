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
                <form action="<?= base_url('operateur/prefixe/add') ?>" method="POST" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="prefixe" class="form-control" placeholder="Ex: 034" required maxlength="10">
                        </div>
                        <div class="col-md-5">
                            <select name="id_operateur" class="form-select" required>
                                <option value="">Sélectionner un opérateur</option>
                                <?php foreach ($operateurs as $op): ?>
                                    <option value="<?= $op['id'] ?>"><?= $op['nom'] ?> (<?= $op['type_reseau'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Ajouter</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Préfixe</th>
                                <th>Opérateur</th>
                                <th>Réseau</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($prefixes as $p): ?>
                                <tr>
                                    <td><?= $p['prefixe'] ?></td>
                                    <td><?= $p['operateur'] ?></td>
                                    <td><?= $p['type_reseau'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3 fw-bold">Commission Inter-Opérateur</div>
    <div class="card-body">
        <form action="<?= base_url('operateur/commission/update') ?>" method="POST" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Opérateur externe</label>
                <select name="id_operateur" class="form-select" required>
                    <option value="">Sélectionner</option>
                    <?php foreach ($operateurs as $op): ?>
                        <?php if ($op['type_reseau'] === 'externe'): ?>
                            <option value="<?= $op['id'] ?>"><?= $op['nom'] ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Pourcentage commission (%)</label>
                <input type="number" step="0.01" min="0" name="pourcentage" class="form-control" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-warning w-100">Enregistrer</button>
            </div>
        </form>
        <div class="table-responsive mt-4">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Opérateur</th>
                        <th>Commission (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commissions as $c): ?>
                        <tr>
                            <td><?= $c['nom'] ?></td>
                            <td><?= number_format($c['pourcentage'], 2, ',', ' ') ?> %</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Configuration des barèmes -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3 fw-bold">Configuration des Barèmes de Frais</div>
    <div class="card-body">
        <?php $baremeTypes = array_values(array_unique(array_column($baremes, 'type'))); ?>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="bareme-type-filter" class="form-label">Filtrer par type d'opération</label>
                <select id="bareme-type-filter" class="form-select">
                    <option value="">Tous</option>
                    <?php foreach ($baremeTypes as $type): ?>
                        <option value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"><?= $type ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table id="baremes-table" class="table table-hover align-middle mb-0">
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
                    <tr data-type="<?= htmlspecialchars($b['type'], ENT_QUOTES, 'UTF-8') ?>">
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filter = document.getElementById('bareme-type-filter');
        const rows = document.querySelectorAll('#baremes-table tbody tr');

        filter.addEventListener('change', function() {
            const value = this.value;
            rows.forEach(function(row) {
                row.style.display = value === '' || row.dataset.type === value ? '' : 'none';
            });
        });
    });
</script>
<?= $this->endSection() ?>