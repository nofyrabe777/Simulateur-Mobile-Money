<?php
namespace App\Controllers;

use App\Models\BaremesModel;

class BaremeController extends BaseController
{
    public function update()
    {
        $baremeModel = new BaremesModel();
        $id = $this->request->getPost('id');

        $data = [
            'montant_min' => (float)$this->request->getPost('montant_min'),
            'montant_max' => (float)$this->request->getPost('montant_max'),
            'frais'       => (float)$this->request->getPost('frais')
        ];

        if ($baremeModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Barème mis à jour avec succès.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la mise à jour.');
    }
}