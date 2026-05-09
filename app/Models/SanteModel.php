<?php

namespace App\Models;

use CodeIgniter\Model;

class SanteModel extends Model
{
    protected $table      = 'sante';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'taille',
        'poids',
        'imc',
        'id_objectif',
    ];

    protected $useTimestamps = true;
    protected $updatedField  = 'updated_at';
    protected $createdField  = '';          // pas de created_at dans la table

    /**
     * Récupère les données de santé d'un utilisateur avec son objectif.
     */
    public function getByUser(int $userId): ?array
    {
        return $this->select('sante.*, objectif.nom AS objectif_nom')
                    ->join('objectif', 'objectif.id = sante.id_objectif', 'left')
                    ->where('sante.user_id', $userId)
                    ->first();
    }

    /**
     * Met à jour l'IMC en recalculant depuis taille et poids.
     */
    public function recalcImc(int $userId): void
    {
        $sante = $this->where('user_id', $userId)->first();
        if ($sante && $sante['taille'] > 0) {
            $tailleM = $sante['taille'] / 100;
            $imc     = round($sante['poids'] / ($tailleM ** 2), 2);
            $this->update($sante['id'], ['imc' => $imc]);
        }
    }
}