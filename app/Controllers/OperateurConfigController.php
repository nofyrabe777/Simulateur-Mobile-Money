<?php
namespace App\Controllers;

use App\Models\OperateurModel;
use App\Models\InteroperatorCommissionModel;

class OperateurConfigController extends BaseController
{
    public function updateCommission()
    {
        $commissionModel = new InteroperatorCommissionModel();
        $idOperateur = (int)$this->request->getPost('id_operateur');
        $pourcentage = (float)$this->request->getPost('pourcentage');

        if ($idOperateur <= 0 || $pourcentage < 0) {
            return redirect()->back()->with('error', 'Données de commission invalides.');
        }

        $commission = $commissionModel->where('id_operateur', $idOperateur)->first();
        if ($commission) {
            $commissionModel->update($commission['id'], ['pourcentage' => $pourcentage]);
        } else {
            $commissionModel->insert(['id_operateur' => $idOperateur, 'pourcentage' => $pourcentage]);
        }

        return redirect()->back()->with('success', 'Commission inter-opérateur mise à jour.');
    }
}
