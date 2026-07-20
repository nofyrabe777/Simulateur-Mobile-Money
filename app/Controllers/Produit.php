<?php

namespace App\Controllers;

class Produit extends BaseController
{
    public function index()
    {
        return "Liste des produits";
    }

    public function show($id)
    {
        return "Produit ID : " .$id;
    }
}