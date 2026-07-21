<?php
namespace App\Controllers;

use App\Models\EpargneModel;

class EpargneController extends BaseController
{
    public function update()
    {
        $epargneModel = new EpargneModel();
        $id = $this->request->getPost('id');

        $data = [
            'pourcentage' => (float)$this->request->getPost('pourcentage'),
        ];
        if ($epargnemeModel->update($id, $data)) {
            return redirect()->back()->with('success', 'epargne mis à jour avec succès.');
        }
        return redirect()->back()->with('error', 'Erreur lors de la mise à jour.');
    }
}