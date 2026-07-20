<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-primary text-white p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase mb-1 text-white-50">Solde Disponible</h6>
                    <h1 class="display-5 fw-bold"><?= number_format($compte['solde'], 2, ',', ' ') ?> <small class="fs-4">Ar</small></h1>
                </div>
                <div>
                    <button class="btn btn-light me-2" data-bs-toggle="modal" data-bs-target="#modalDepot"> Dépôt</button>
                    <button class="btn btn-light me-2" data-bs-toggle="modal" data-bs-target="#modalRetrait"> Retrait</button>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalTransfert"> Transfert</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Historique récent des transactions</h5>
        <a href="<?= base_url('client/historique') ?>" class="btn btn-outline-secondary btn-sm">Voir tout l'historique</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date & Heure</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Frais</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historique)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Aucune transaction enregistrée.</td>
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
                            <td class="fw-bold text-dark"><?= number_format($tx['montant'], 2, ',', ' ') ?> Ar</td>
                            <td class="text-muted"><?= number_format($tx['frais'], 2, ',', ' ') ?> Ar</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODALE DÉPÔT (type_operation = 1) ================= -->
<div class="modal fade" id="modalDepot" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('client/transaction') ?>" method="POST" class="modal-content">
            <input type="hidden" name="type_operation" value="1">
            <div class="modal-header">
                <h5 class="modal-title">Faire un dépôt automatique</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Montant à déposer (Ar)</label>
                    <input type="number" step="0.01" name="montant" class="form-control" required min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-success">Confirmer le dépôt</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODALE RETRAIT (type_operation = 2) ================= -->
<div class="modal fade" id="modalRetrait" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('client/transaction') ?>" method="POST" class="modal-content">
            <input type="hidden" name="type_operation" value="2">
            <div class="modal-header">
                <h5 class="modal-title">Faire un retrait automatique</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Montant à retirer (Ar)</label>
                    <input type="number" step="0.01" name="montant" class="form-control" required min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger">Confirmer le retrait</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODALE TRANSFERT (type_operation = 3) ================= -->
<div class="modal fade" id="modalTransfert" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('client/transaction') ?>" method="POST" class="modal-content">
            <input type="hidden" name="type_operation" value="3">
            <div class="modal-header">
                <h5 class="modal-title">Effectuer un transfert d'argent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Numéro du destinataire</label>
                    <input type="text" name="destinataire" class="form-control" placeholder="Ex: 033XXXXXXXX" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Montant à transférer (Ar)</label>
                    <input type="number" step="0.01" name="montant" class="form-control" required min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-warning">Envoyer l'argent</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>