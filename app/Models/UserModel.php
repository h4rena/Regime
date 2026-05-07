<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'nom',
        'prenom',
        'email',
        'password',
        'genre_id',
        'Date_naissance',
        'wallet_balance',
        'is_gold',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'nom'            => 'permit_empty|min_length[2]|max_length[100]',
        'prenom'         => 'permit_empty|min_length[2]|max_length[100]',
        'email'          => 'permit_empty|valid_email',
        'password'       => 'permit_empty|min_length[8]',
        'genre_id'       => 'permit_empty|integer',
        'Date_naissance' => 'permit_empty|valid_date[Y-m-d]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
}
