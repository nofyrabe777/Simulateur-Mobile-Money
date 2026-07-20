<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\PrefixeModel;
use App\Models\BaremesModel;
use App\Models\TransactionsModel;
use App\Models\OperateurModel;
use App\Models\InteroperatorCommissionModel;

class OperateurController extends BaseController
{
    public function dashboard()
    {
        $compteModel = new CompteModel();
        $prefixeModel = new PrefixeModel();
        $baremeModel = new BaremesModel();
        $txModel = new TransactionsModel();

        // 1. Calcul des gains via les frais de transactions (Type 2 = Retrait, Type 3 = Transfert)
        $gains_retrait = $txModel->where('id_type_operation', 2)->selectSum('frais')->first()['frais'] ?? 0;
        $gains_transfert = $txModel->where('id_type_operation', 3)->selectSum('frais')->first()['frais'] ?? 0;
        $gains_totaux = $gains_retrait + $gains_transfert;

        // 2. Récupération des données pour les tableaux
        $clients = $compteModel->findAll();
        $prefixes = $prefixeModel->select('prefixes.*, operateurs.nom as operateur, operateurs.type_reseau')
                                ->join('operateurs', 'operateurs.id = prefixes.id_operateur')
                                ->findAll();
        $operateurs = (new \App\Models\OperateurModel())->findAll();
        $commissions = (new \App\Models\InteroperatorCommissionModel())
                        ->select('interoperator_commissions.*, operateurs.nom')
                        ->join('operateurs', 'operateurs.id = interoperator_commissions.id_operateur')
                        ->findAll();
        
        // Jointure pour afficher le nom du type d'opération lisiblement
        $baremes = $baremeModel->select('baremes.*, type_operations.nom as type')
                              ->join('type_operations', 'type_operations.id = baremes.id_type_operation')
                              ->findAll();

        // 3. Envoi complet des variables à la vue
        return view('operateur/dashboard', [
            'gains_retrait'   => $gains_retrait,
            'gains_transfert' => $gains_transfert,
            'gains_totaux'    => $gains_totaux,
            'clients'         => $clients,
            'prefixes'        => $prefixes,
            'operateurs'      => $operateurs,
            'commissions'     => $commissions,
            'baremes'         => $baremes
        ]);
    }
}