<?php

namespace App\Models;
use CodeIgniter\Model;
// use App\Models\ProduitModel;

class ProduitModel extends Model
    {
    protected $table = 'produits';
    protected $allowedFields = ['nom', 'prix'];

    // public function index()
    // {
    // $model = new ProduitModel();
    // $data['produits'] = $model->findAll();
    // return view('produits', $data);
    // }
    }


