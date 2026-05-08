<?php

namespace App\Models;

use CodeIgniter\Model;

class ActiviteModel extends Model
{
    protected $table      = 'activite_sportive';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'nom',
        'calories_par_heure',
        'duree_recommandee_min',
    ];

    protected $useTimestamps = false;
}
