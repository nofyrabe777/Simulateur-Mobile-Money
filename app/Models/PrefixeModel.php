<?php

    namespace App\Models;

    use CodeIgniter\Model;

    class PrefixeModel extends Model
    {
        protected $table            = 'prefixes';
        protected $primaryKey       = 'id';
        protected $useAutoIncrement = true;
        protected $returnType       = 'array';
        protected $allowedFields    = ['prefixe'];
    }

?>