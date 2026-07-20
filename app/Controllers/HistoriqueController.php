<?php
namespace App\Controllers;

use App\Models\TransactionsModel;

class HistoriqueController extends BaseController
{
    public function index()
    {
        $session = session()->get('client_session');
        if (!$session) return redirect()->to('/');

        $txModel = new TransactionsModel();
        
        $data['historique'] = $txModel->select('transactions.*, type_operations.nom as type')
                                      ->join('type_operations', 'type_operations.id = transactions.id_type_operation')
                                      ->groupStart()
                                          ->where('id_compte_expediteur', $session['id'])
                                          ->orWhere('id_compte_destinataire', $session['id'])
                                      ->groupEnd()
                                      ->orderBy('date_transaction', 'DESC')
                                      ->findAll();

        return view('client/historique', $data);
    }
}