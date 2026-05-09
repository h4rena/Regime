<?php

namespace App\Models;

use CodeIgniter\Model;

class WalletCodeModel extends Model
{
    // Table name matches the SQL schema: wallet_codes
    protected $table            = 'wallet_codes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'montant',
        'is_used',
        'used_by',
        'used_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'montant' => 'float',
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'code'    => 'required|min_length[3]|max_length[20]|is_unique[wallet_codes.code]',
        'montant' => 'required|decimal',
        'is_used' => 'permit_empty|in_list[0,1]'
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


public function updateWalletCode($code, $userId)
{
    // Validate
    if (empty($userId) || empty($code)) {
        return [
            'status'  => 'error',
            'message' => 'Utilisateur ou code manquant.'
        ];
    }

    // Attempt atomic update: only update when used_by IS NULL to prevent races
    $now = date('Y-m-d H:i:s');

    $builder = $this->builder();
    $builder->where('code', $code);
    $builder->where('used_by', null);
    $builder->set(['is_used' => 1, 'used_by' => $userId, 'used_at' => $now]);

    try {
        $success = $builder->update();
    } catch (\Exception $e) {
        return [
            'status'  => 'error',
            'message' => 'Erreur lors de la mise à jour du code: ' . $e->getMessage()
        ];
    }

    // If no rows affected, either code doesn't exist or was already used
    $affected = $this->db->affectedRows();
    if ($affected <= 0) {
        // Check if code exists
        $exists = $this->where('code', $code)->first();
        if (!$exists) {
            return [
                'status'  => 'error',
                'message' => 'Ce code est inexistant.'
            ];
        }

        return [
            'status'  => 'error',
            'message' => 'Ce code a déjà été utilisé.'
        ];
    }

    // Retrieve montant to credit
    $walletCode = $this->where('code', $code)->first();
    $montant = $walletCode['montant'] ?? 0;

    // Crédite le wallet de l'utilisateur (increment)
    $Wallet = new WalletModel();

    $this->db->transStart();
    $Wallet->updateMontantWallet($montant, $userId);

    $this->db->table('wallet_transactions')->insert([
        'user_id' => (int) $userId,
        'montant' => (float) $montant,
        'type'    => 'credit',
    ]);
    $this->db->transComplete();

    return [
        'status'  => 'success',
        'message' => 'Code appliqué avec succès.'
    ];
}

}


