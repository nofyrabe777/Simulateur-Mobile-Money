<?php
namespace App\Controllers;

use App\Models\BaremesModel;
use App\Models\CompteModel;
use App\Models\PrefixeModel;
use App\Models\TransactionsModel;

class OperateurController extends BaseController
{
    public function dashboard()
    {
        $prefixeModel = new PrefixeModel();
        $compteModel = new CompteModel();
        $baremeModel = new BaremesModel();
        $transactionModel = new TransactionsModel();

        $prefixes = $prefixeModel->findAll();
        $comptes = $compteModel->orderBy('created_at', 'DESC')->findAll();
        $baremes = $baremeModel->orderBy('id_type_operation', 'ASC')->orderBy('montant_min', 'ASC')->findAll();

        $totalGains = $transactionModel->selectSum('frais')->first();
        $totalClients = $compteModel->countAllResults();

        return view('operateur/dashboard', [
            'prefixes' => $prefixes,
            'comptes' => $comptes,
            'baremes' => $baremes,
            'totalGains' => (float) ($totalGains['frais'] ?? 0),
            'totalClients' => $totalClients,
        ]);
    }
}
