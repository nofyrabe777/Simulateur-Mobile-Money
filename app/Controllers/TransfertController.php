<?php
namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\TransactionsModel;
use App\Models\BaremesModel;

class TransfertController extends BaseController
{
    public function store()
    {
        $session = session()->get('client_session');
        if (!$session) return redirect()->to('/');

        $montant = (float)$this->request->getPost('montant');
        $destTel = $this->request->getPost('destinataire');

        $compteModel = new CompteModel();
        $destinataire = $compteModel->where('telephone', $destTel)->first();

        if (!$destinataire) {
            return redirect()->back()->with('error', 'Le compte destinataire n\'existe pas.');
        }

        if ($session['telephone'] === $destTel) {
            return redirect()->back()->with('error', 'Impossible de transférer vers votre propre numéro.');
        }

        // Trouver le barème (id_type_operation = 3 pour Transfert)
        $baremeModel = new BaremesModel();
        $bareme = $baremeModel->where('id_type_operation', 3)
                              ->where('montant_min <=', $montant)
                              ->where('montant_max >=', $montant)
                              ->first();

        if (!$bareme) {
            return redirect()->back()->with('error', 'Montant hors tranches de transfert.');
        }

        $frais = (float)$bareme['frais'];

        $db = \Config\Database::connect();
        $db->transStart();

        $expediteur = $compteModel->find($session['id']);

        if ($expediteur['solde'] < ($montant + $frais)) {
            return redirect()->back()->with('error', 'Solde insuffisant pour le transfert et ses frais.');
        }

        // Mouvements de solde
        $compteModel->update($expediteur['id'], ['solde' => $expediteur['solde'] - ($montant + $frais)]);
        $compteModel->update($destinataire['id'], ['solde' => $destinataire['solde'] + $montant]);

        // Tracer la transaction
        $txModel = new TransactionsModel();
        $txModel->insert([
            'id_type_operation'     => 3,
            'id_compte_expediteur'   => $expediteur['id'],
            'id_compte_destinataire' => $destinataire['id'],
            'montant'                => $montant,
            'frais'                  => $frais
        ]);

        $db->transComplete();

        return redirect()->to('/client/dashboard')->with('success', 'Transfert envoyé avec succès.');
    }
}