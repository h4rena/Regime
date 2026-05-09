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
        'type'    => 'in_list[gold,normal]'
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

    public function ensureWalletExists(int $userId): array
    {
        $wallet = $this->where('user_id', $userId)->first();

        if (! empty($wallet)) {
            return $wallet;
        }

        $walletId = $this->insert([
            'user_id' => $userId,
            'type'    => 'normal',
            'montant' => 0,
        ], true);

        if (! $walletId) {
            return ['user_id' => $userId, 'montant' => 0, 'type' => 'normal'];
        }

        return $this->find($walletId) ?? ['user_id' => $userId, 'montant' => 0, 'type' => 'normal'];
    }

    public function getTransactionHistory(int $userId, int $limit = 20): array
    {
        if ($userId <= 0) {
            return [];
        }

        return $this->db->table('wallet_transactions')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function updateMontantWallet($montant, $userId)
    {
        return $this->changeWalletBalance((int) $userId, (float) $montant, 'credit');
    }

    public function debitMontantWallet($montant, $userId)
    {
        return $this->changeWalletBalance((int) $userId, (float) $montant, 'debit');
    }

    private function changeWalletBalance(int $userId, float $amount, string $direction): bool
    {
        if ($userId <= 0 || $amount <= 0) {
            return false;
        }

        $wallet = $this->ensureWalletExists($userId);
        $currentBalance = (float) ($wallet['montant'] ?? 0);

        if ($direction === 'debit' && $currentBalance < $amount) {
            return false;
        }

        $newBalance = $direction === 'debit'
            ? $currentBalance - $amount
            : $currentBalance + $amount;

        $db = $this->db;
        $db->transStart();

        $this->where('user_id', $userId)->set(['montant' => $newBalance])->update();
        $db->table('users')->where('id', $userId)->update(['wallet_balance' => $newBalance]);

        $db->transComplete();

        return $db->transStatus();
    }

}
