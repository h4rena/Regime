<?php

namespace App\Models;

use CodeIgniter\Model;

class RegimeModel extends Model
{
    protected $table      = 'regime';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'nom',
        'variation_poids',
        'duree_jours',
        'prix',
    ];

    protected $useTimestamps = false;
}
