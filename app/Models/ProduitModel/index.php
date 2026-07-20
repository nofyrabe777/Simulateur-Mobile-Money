<?php

use App\Models\ProduitModel;

public function index()
{
    $model = new ProduitModel();
    $data['produits'] = $model->findAll();

    return view('produits',$data);
}