<?php

    namespace App\Models;

    use CodeIgniter\Model;

    class CompteModel extends Model
    {
        protected $table            = 'compte';
        protected $primaryKey       = 'id';
        protected $useAutoIncrement = true;
        protected $returnType       = 'array';
        protected $allowedFields    = ['telephone', 'solde'];

        // Si vous préférez que CodeIgniter gère automatiquement le created_at
        protected $useTimestamps    = true;
        protected $createdField     = 'created_at';
        protected $updatedField     = ''; // On le laisse vide car il n'existe pas en BDD
    }

?>