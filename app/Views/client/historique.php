<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Relevé Complet des Opérations</h3>
    <a href="<?= base_url('client/dashboard') ?>" class="btn btn-secondary btn-sm">← Retour au Tableau de bord</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Date & Heure de l'opération</th>
                    <th>Type d'opération</th>
                    <th>Montant Net</th>
                    <th>Frais de service</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historique)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">Aucun flux financier sur ce compte.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($historique as $tx): ?>
                        <tr>
                            <td><?= $tx['date_transaction'] ?></td>
                            <td>
                                <span class="badge <?= $tx['id_type_operation'] == 1 ? 'bg-success' : ($tx['id_type_operation'] == 2 ? 'bg-danger' : 'bg-info') ?>">
                                    <?= $tx['type'] ?>
                                </span>
                            </td>
                            <td class="fw-bold"><?= number_format($tx['montant'], 2, ',', ' ') ?> Ar</td>
                            <td class="text-danger fw-semibold"><?= $tx['frais'] > 0 ? '-' . number_format($tx['frais'], 2, ',', ' ') . ' Ar' : 'Gratuit' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>