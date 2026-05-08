<?php

namespace App\Models;

use CodeIgniter\Model;

class WalletModel extends Model
{
    protected $table            = 'wallet';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // Champs autorisés
    protected $allowedFields    = [
        'user_id',
        'type',
        'montant'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Casts automatiques
    // Note: DataCaster does not provide a 'string' handler, so do not declare 'type' => 'string'.
    protected array $casts = [
        'montant' => 'float',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false; // pas de created_at/updated_at dans cette table
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'user_id' => 'required|integer',
        'montant' => 'required|decimal',
        'type'    => 'in_list[golde,normal]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Récupère le wallet unique d'un utilisateur
     */
    public function getWalletUser($userId)
    {
        if (empty($userId)) {
            return ['user_id' => 0, 'montant' => 0, 'type' => 'normal'];
        }

        $wallet = $this->where('user_id', $userId)->first();

        // Ensure we always return an array with expected keys so views don't trigger warnings
        if (empty($wallet)) {
            return ['user_id' => $userId, 'montant' => 0, 'type' => 'normal'];
        }

        return $wallet;
    }
    public function updateMontantWallet($montant, $userId)
    {
        if (empty($userId)) {
            return false;
        }

        return $this->where('user_id', $userId)
                    ->set('montant', $montant)
                    ->update();
    }

}
