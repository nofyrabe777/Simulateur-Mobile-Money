<?php
namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\TransactionsModel;

class DepotController extends BaseController
{
    public function store()
    {
        $session = session()->get('client_session');
        if (!$session) return redirect()->to('/');

        $montant = (float)$this->request->getPost('montant');
        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $compteModel = new CompteModel();
        $compte = $compteModel->find($session['id']);

        // Créditer le compte
        $compteModel->update($compte['id'], ['solde' => $compte['solde'] + $montant]);

        // Enregistrer l'opération (id_type_operation = 1 pour Dépôt)
        $txModel = new TransactionsModel();
        $txModel->insert([
            'id_type_operation'     => 1,
            'id_compte_expediteur'   => $compte['id'],
            'id_compte_destinataire' => null,
            'montant'                => $montant,
            'frais'                  => 0.0
        ]);

        $db->transComplete();

        return redirect()->to('/client/dashboard')->with('success', 'Dépôt effectué avec succès.');
    }
}