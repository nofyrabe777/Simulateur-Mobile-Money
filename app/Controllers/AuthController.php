<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\PrefixeModel;

class AuthController extends BaseController
{
    /**
     * Traite la soumission du formulaire de connexion (POST)
     * URL: /auth/login
     */
   public function login()
        {
            $tel = $this->request->getPost('tel');
            
            if (empty($tel)) {
                return redirect()->back()->with('error', 'Le numéro de téléphone est requis.');
            }

            // 1. Vérification du préfixe par rapport à la configuration opérateur
            $prefixeModel = new \App\Models\PrefixeModel();
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

            // 2. Connexion ou création automatique du compte
            $compteModel = new \App\Models\CompteModel();
            
            // Recherche stricte sur la colonne 'telephone'
            $compte = $compteModel->where('telephone', $tel)->first();

            if (!$compte) {
                $id = $compteModel->insert([
                    'telephone' => $tel,
                    'solde'     => 0.0
                ]);
                $compte = $compteModel->find($id);
            }

            // 3. Enregistrement en session et redirection
            session()->set('client_session', $compte);
            return redirect()->to('/client/dashboard')->with('success', 'Connexion réussie !');
        }

    /**
     * Déconnexion de l'espace client
     * URL: /auth/logout
     */
    public function logout()
    {
        session()->remove('client_session');
        return redirect()->to('/')->with('success', 'Vous avez été déconnecté.');
    }
}