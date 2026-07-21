<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row gx-4">
    <div class="col-xl-3 col-lg-4 mb-4">
        <div class="card shadow-sm rounded-4 h-100" style="background: linear-gradient(180deg, #0d6efd 0%, #2563eb 100%);">
            <div class="card-body p-4 text-white">
                <h5 class="fw-bold mb-3">Opérateur</h5>
                <p class="small text-white-75 mb-4">Gestion des paramètres et des barèmes.</p>
                <div class="list-group list-group-flush">
                    <button type="button" class="list-group-item list-group-item-action active bg-transparent border-0 text-white p-3 text-start" data-section="dashboard-section">Dashboard</button>
                    <button type="button" class="list-group-item list-group-item-action bg-transparent border-0 text-white p-3 text-start" data-section="clients-section">Comptes clients</button>
                    <button type="button" class="list-group-item list-group-item-action bg-transparent border-0 text-white p-3 text-start" data-section="prefix-section">Préfixes</button>
                    <button type="button" class="list-group-item list-group-item-action bg-transparent border-0 text-white p-3 text-start" data-section="commission-section">Commissions</button>
                    <button type="button" class="list-group-item list-group-item-action bg-transparent border-0 text-white p-3 text-start" data-section="bareme-section">Barèmes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-9 col-lg-8">
        <div id="dashboard-section" class="action-section">
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-4">
                    <div class="card shadow-sm rounded-4 border-0 h-100">
                        <div class="card-body p-4">
                            <p class="text-uppercase text-muted small mb-2">Gains Retraits</p>
                            <h3 class="fw-bold mb-0"><?= number_format($gains_retrait, 2, ',', ' ') ?> Ar</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="card shadow-sm rounded-4 border-0 h-100">
                        <div class="card-body p-4">
                            <p class="text-uppercase text-muted small mb-2">Gains Transferts</p>
                            <h3 class="fw-bold mb-0"><?= number_format($gains_transfert, 2, ',', ' ') ?> Ar</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="card shadow-sm rounded-4 border-0 h-100">
                        <div class="card-body p-4">
                            <p class="text-uppercase text-muted small mb-2">Gains Totaux</p>
                            <h3 class="fw-bold mb-0"><?= number_format($gains_totaux, 2, ',', ' ') ?> Ar</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="card-title mb-1">Vue clients</h5>
                            <p class="text-muted small mb-0">Liste des comptes clients.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Téléphone</th>
                                    <th>Solde actuel</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($clients as $c): ?>
                                    <tr>
                                        <td><?= $c['tel'] ?? $c['telephone'] ?></td>
                                        <td class="fw-bold"><?= number_format($c['solde'], 2, ',', ' ') ?> Ar</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="clients-section" class="action-section d-none">
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Comptes clients</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Téléphone</th>
                                    <th>Solde actuel</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($clients as $c): ?>
                                    <tr>
                                        <td><?= $c['tel'] ?? $c['telephone'] ?></td>
                                        <td class="fw-bold"><?= number_format($c['solde'], 2, ',', ' ') ?> Ar</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="prefix-section" class="action-section d-none">
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Préfixes</h5>
                    <form action="<?= base_url('operateur/prefixe/add') ?>" method="POST" class="row g-3 mb-4">
                        <div class="col-sm-5">
                            <input type="text" name="prefixe" class="form-control" placeholder="Ex: 034" required maxlength="10">
                        </div>
                        <div class="col-sm-5">
                            <select name="id_operateur" class="form-select" required>
                                <option value="">Sélectionner un opérateur</option>
                                <?php foreach ($operateurs as $op): ?>
                                    <option value="<?= $op['id'] ?>"><?= $op['nom'] ?> (<?= $op['type_reseau'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-primary w-100">Ajouter</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
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

        <div id="commission-section" class="action-section d-none">
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Commissions</h5>
                    <form action="<?= base_url('operateur/commission/update') ?>" method="POST" class="row g-3 align-items-end mb-4">
                        <div class="col-sm-5">
                            <select name="id_operateur" class="form-select" required>
                                <option value="">Sélectionner un opérateur externe</option>
                                <?php foreach ($operateurs as $op): ?>
                                    <?php if ($op['type_reseau'] === 'externe'): ?>
                                        <option value="<?= $op['id'] ?>"><?= $op['nom'] ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-5">
                            <input type="number" step="0.01" min="0" name="pourcentage" class="form-control" placeholder="Commission (%)" required>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-warning w-100">Enregistrer</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
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
        </div>

        <div id="bareme-section" class="action-section d-none">
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Barèmes</h5>
                    <?php $baremeTypes = array_values(array_unique(array_column($baremes, 'type'))); ?>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <select id="bareme-type-filter" class="form-select">
                                <option value="">Tous les types</option>
                                <?php foreach ($baremeTypes as $type): ?>
                                    <option value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"><?= $type ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="baremes-table" class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Tranche Min</th>
                                    <th>Tranche Max</th>
                                    <th>Frais</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($baremes as $b): ?>
                                    <tr data-type="<?= htmlspecialchars($b['type'], ENT_QUOTES, 'UTF-8') ?>">
                                        <form action="<?= base_url('operateur/bareme/update') ?>" method="POST">
                                            <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                            <td><span class="badge bg-secondary"><?= $b['type'] ?></span></td>
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
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navButtons = document.querySelectorAll('.list-group-item-action');
        const sections = document.querySelectorAll('.action-section');
        const filter = document.getElementById('bareme-type-filter');
        const rows = document.querySelectorAll('#baremes-table tbody tr');

        function showSection(id) {
            sections.forEach(section => {
                section.classList.toggle('d-none', section.id !== id);
            });
            navButtons.forEach(button => {
                button.classList.toggle('active', button.dataset.section === id);
            });
        }

        navButtons.forEach(button => {
            button.addEventListener('click', function() {
                showSection(this.dataset.section);
            });
        });

        if (filter) {
            filter.addEventListener('change', function() {
                const value = this.value;
                rows.forEach(function(row) {
                    row.style.display = value === '' || row.dataset.type === value ? '' : 'none';
                });
            });
        }

        showSection('dashboard-section');
    });
</script>
<?= $this->endSection() ?>
