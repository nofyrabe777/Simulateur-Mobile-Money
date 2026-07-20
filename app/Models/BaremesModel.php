<?php

    namespace App\Models;

    use CodeIgniter\Model;

    class BaremesModel extends Model
    {
        protected $table            = 'baremes';
        protected $primaryKey       = 'id';
        protected $useAutoIncrement = true;
        protected $returnType       = 'array';
        protected $allowedFields    = ['id_type_operation', 'montant_min', 'montant_max', 'frais'];
    }

?>