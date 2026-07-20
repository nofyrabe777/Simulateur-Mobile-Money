<?php

namespace App\Models;

use CodeIgniter\Model;

class InteroperatorCommissionModel extends Model
{
    protected $table            = 'interoperator_commissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_operateur', 'pourcentage'];
}
