<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionsModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Champs autorisés à l'insertion
    protected $allowedFields    = [
        'id_type_operation',
        'id_compte_expediteur',
        'id_compte_destinataire',
        'montant',
        'frais',
        'date_transaction'
    ];

    protected $useTimestamps = false; // La date est gérée par SQLite (DEFAULT CURRENT_TIMESTAMP)


}
