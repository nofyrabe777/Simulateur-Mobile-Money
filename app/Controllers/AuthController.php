<?php
namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\PrefixeModel;

class AuthController extends BaseController
{
    public function login()
    {
        if ($this->request->getMethod() === 'get') {
            if (session()->get('client_session')) {
                return redirect()->to('/client/dashboard');
            }

            return view('auth/login');
        }

        $tel = $this->request->getPost('tel');
        if (empty($tel)) {
            return redirect()->back()->with('error', 'Le numéro de téléphone est requis.');
        }

        // Vérification du préfixe
        $prefixeModel = new PrefixeModel();
        $prefixes = $prefixeModel->findAll();
        $valide = false;

        foreach ($prefixes as $p) {
            if (strpos($tel, $p['prefixe']) === 0) {
                $valide = true;
                break;
            }
        }

        if (!$valide) {
            return redirect()->back()->with('error', 'Numéro invalide pour cet opérateur.');
        }

        // Connexion ou création automatique du compte
        $compteModel = new CompteModel();
        $compte = $compteModel->where('telephone', $tel)->first();

        if (!$compte) {
            $id = $compteModel->insert([
                'telephone' => $tel,
                'solde'     => 0.0
            ]);
            $compte = $compteModel->find($id);
        }

        session()->set('client_session', $compte);
        return redirect()->to('/client/dashboard');
    }

    public function logout()
    {
        session()->remove('client_session');
        return redirect()->to('/');
    }
}