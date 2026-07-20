<?php
namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\TransactionsModel;
use App\Models\BaremesModel;

class RetraitController extends BaseController
{
    public function store()
    {
        $session = session()->get('client_session');
        if (!$session) return redirect()->to('/');

        $montant = (float)$this->request->getPost('montant');
        
        // Trouver le barème (id_type_operation = 2 pour Retrait)
        $baremeModel = new BaremesModel();
        $bareme = $baremeModel->where('id_type_operation', 2)
                              ->where('montant_min <=', $montant)
                              ->where('montant_max >=', $montant)
                              ->first();

        if (!$bareme) {
            return redirect()->back()->with('error', 'Le montant ne correspond à aucune tranche autorisée.');
        }

        $frais = (float)$bareme['frais'];

        $db = \Config\Database::connect();
        $db->transStart();

        $compteModel = new CompteModel();
        $compte = $compteModel->find($session['id']);

        if ($compte['solde'] < ($montant + $frais)) {
            return redirect()->back()->with('error', 'Solde insuffisant (Frais de ' . $frais . ' incluts).');
        }

        // Débiter le compte (Montant + Frais)
        $compteModel->update($compte['id'], ['solde' => $compte['solde'] - ($montant + $frais)]);

        // Enregistrer la transaction
        $txModel = new TransactionsModel();
        $txModel->insert([
            'id_type_operation'     => 2,
            'id_compte_expediteur'   => $compte['id'],
            'id_compte_destinataire' => null,
            'montant'                => $montant,
            'frais'                  => $frais
        ]);

        $db->transComplete();

        return redirect()->to('/client/dashboard')->with('success', 'Retrait réussi.');
    }
}