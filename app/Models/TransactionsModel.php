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
        'frais_retrait_inclus',
        'commission_interoperateur',
        'id_operateur_destinataire',
        'date_transaction',
        'promotion'
    ];

    protected $useTimestamps = false; // La date est gérée par SQLite (DEFAULT CURRENT_TIMESTAMP)


}
