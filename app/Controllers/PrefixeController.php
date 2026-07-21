<?php
namespace App\Controllers;

use App\Models\PrefixeModel;
use App\Models\OperateurModel;

class PrefixeController extends BaseController
{
    public function store()
    {
        $prefixeModel = new PrefixeModel();
        $operateurModel = new OperateurModel();
        $prefixe = $this->request->getPost('prefixe');
        $idOperateur = (int)$this->request->getPost('id_operateur');

        if (empty($prefixe) || $idOperateur <= 0) {
            return redirect()->back()->with('error', 'Le préfixe et l\'opérateur sont requis.');
        }

        $operateur = $operateurModel->find($idOperateur);
        if (!$operateur) {
            return redirect()->back()->with('error', 'Opérateur invalide.');
        }

        $existing = $prefixeModel->where('prefixe', $prefixe)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Ce préfixe existe déjà.');
        }

        $prefixeModel->insert([
            'prefixe'      => $prefixe,
            'id_operateur' => $idOperateur
        ]);

        return redirect()->back()->with('success', 'Préfixe associé à l\'opérateur ajouté avec succès.');
    }
}