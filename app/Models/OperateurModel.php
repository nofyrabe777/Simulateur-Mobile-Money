<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $table            = 'operateurs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nom', 'type_reseau'];
}
