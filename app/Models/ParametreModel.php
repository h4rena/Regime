<?php

namespace App\Models;

use CodeIgniter\Model;

class ParametreModel extends Model
{
    protected $table      = 'parametres';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'cle',
        'libelle',
        'valeur',
        'type',
        'description',
    ];

    protected $useTimestamps = false;
}
