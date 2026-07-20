<?php
namespace App\Controllers;

use App\Models\PrefixeModel;

class PrefixeController extends BaseController
{
    public function store()
    {
        $prefixeModel = new PrefixeModel();
        $prefixe = $this->request->getPost('prefixe');

        if (!empty($prefixe)) {
            $prefixeModel->insert(['prefixe' => $prefixe]);
            return redirect()->back()->with('success', 'Préfixe ajouté avec succès.');
        }

        return redirect()->back()->with('error', 'Le champ préfixe ne peut pas être vide.');
    }
}