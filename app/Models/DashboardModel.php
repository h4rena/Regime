<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // ─── STATISTIQUES GLOBALES ────────────────────────────────────────────────

    public function getGlobalStats(): array
    {
        $stats = [];

        // Nombre total d'utilisateurs (hors admin)
        $stats['total_users'] = $this->db->table('users')
            ->where('id_role', 2)
            ->countAllResults();

        // Nombre d'utilisateurs Gold
        $stats['gold_users'] = $this->db->table('users')
            ->where('id_role', 2)
            ->where('is_gold', 1)
            ->countAllResults();

        // Nombre total de régimes commandés
        $stats['total_subscriptions'] = $this->db->table('user_regime')
            ->countAllResults();

        // Revenus totaux
        $result = $this->db->query('SELECT COALESCE(SUM(prix_paye), 0) AS total FROM user_regime')->getRow();
        $stats['total_revenue'] = $result->total ?? 0;

        // Codes portefeuille utilisés / total
        $stats['codes_used']  = $this->db->table('wallet_codes')->where('is_used', 1)->countAllResults();
        $stats['codes_total'] = $this->db->table('wallet_codes')->countAllResults();

        // Solde total des wallets
        $result = $this->db->query('SELECT COALESCE(SUM(montant), 0) AS total FROM wallet')->getRow();
        $stats['total_wallet_balance'] = $result->total ?? 0;

        // Nombre total de régimes définis
        $stats['total_regimes'] = $this->db->table('regime')->countAllResults();

        // Nombre total d'activités
        $stats['total_activites'] = $this->db->table('activite_sportive')->countAllResults();

        return $stats;
    }

    // ─── INSCRIPTIONS PAR MOIS (12 derniers mois) ────────────────────────────

    public function getUsersPerMonth(): array
    {
        $sql = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') AS mois,
                DATE_FORMAT(created_at, '%b %Y')  AS label,
                COUNT(*) AS total
            FROM users
            WHERE id_role = 2
              AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY mois ASC
        ";
        return $this->db->query($sql)->getResultArray();
    }

    // ─── RÉGIMES LES PLUS SOUSCRITS ──────────────────────────────────────────

    public function getPopularRegimes(): array
    {
        $sql = "
            SELECT r.nom, COUNT(ur.id) AS total, SUM(ur.prix_paye) AS revenus
            FROM user_regime ur
            JOIN regime r ON r.id = ur.regime_id
            GROUP BY r.id, r.nom
            ORDER BY total DESC
            LIMIT 6
        ";
        return $this->db->query($sql)->getResultArray();
    }

    // ─── DISTRIBUTION DES OBJECTIFS ──────────────────────────────────────────

    public function getObjectifsDistribution(): array
    {
        $sql = "
            SELECT o.nom, COUNT(s.id) AS total
            FROM sante s
            JOIN objectif o ON o.id = s.id_objectif
            GROUP BY o.id, o.nom
        ";
        return $this->db->query($sql)->getResultArray();
    }

    // ─── STATISTIQUES CODES PORTEFEUILLE ─────────────────────────────────────

    public function getWalletCodesStats(): array
    {
        $sql = "
            SELECT 
                SUM(is_used = 0) AS disponibles,
                SUM(is_used = 1) AS utilises,
                COALESCE(SUM(CASE WHEN is_used = 1 THEN montant ELSE 0 END), 0) AS montant_distribue
            FROM wallet_codes
        ";
        return (array) $this->db->query($sql)->getRow();
    }

    // ─── GOLD VS NORMAL ──────────────────────────────────────────────────────

    public function getGoldVsNormal(): array
    {
        $sql = "
            SELECT 
                SUM(is_gold = 1) AS gold,
                SUM(is_gold = 0) AS normal
            FROM users
            WHERE id_role = 2
        ";
        return (array) $this->db->query($sql)->getRow();
    }

    // ─── REVENUS PAR RÉGIME ──────────────────────────────────────────────────

    public function getRevenuePerRegime(): array
    {
        $sql = "
            SELECT r.nom, COALESCE(SUM(ur.prix_paye), 0) AS revenus
            FROM regime r
            LEFT JOIN user_regime ur ON ur.regime_id = r.id
            GROUP BY r.id, r.nom
            ORDER BY revenus DESC
        ";
        return $this->db->query($sql)->getResultArray();
    }

    // ─── ACTIVITÉS LES PLUS ASSOCIÉES ────────────────────────────────────────

    public function getPopularActivites(): array
    {
        $sql = "
            SELECT a.nom, COUNT(ura.id) AS total
            FROM activite_sportive a
            LEFT JOIN user_regime_activite ura ON ura.activite_id = a.id
            GROUP BY a.id, a.nom
            ORDER BY total DESC
            LIMIT 5
        ";
        return $this->db->query($sql)->getResultArray();
    }

    // ─── DISTRIBUTION IMC ────────────────────────────────────────────────────

    public function getImcDistribution(): array
    {
        $sql = "
            SELECT
                SUM(imc < 18.5)              AS sous_poids,
                SUM(imc BETWEEN 18.5 AND 24.9) AS normal,
                SUM(imc BETWEEN 25 AND 29.9)   AS surpoids,
                SUM(imc >= 30)               AS obesite
            FROM sante
            WHERE imc IS NOT NULL
        ";
        $row = $this->db->query($sql)->getRow();
        return [
            ['label' => 'Sous-poids (<18.5)',    'total' => (int)($row->sous_poids ?? 0)],
            ['label' => 'Normal (18.5–24.9)',     'total' => (int)($row->normal    ?? 0)],
            ['label' => 'Surpoids (25–29.9)',     'total' => (int)($row->surpoids  ?? 0)],
            ['label' => 'Obésité (≥30)',          'total' => (int)($row->obesite   ?? 0)],
        ];
    }

    // ─── DERNIERS UTILISATEURS INSCRITS ──────────────────────────────────────

    public function getRecentUsers(int $limit = 5): array
    {
        $sql = "
            SELECT u.id, u.nom, u.prenom, u.email, u.is_gold,
                   u.created_at, g.nom AS genre
            FROM users u
            LEFT JOIN genre g ON g.id = u.genre_id
            WHERE u.id_role = 2
            ORDER BY u.created_at DESC
            LIMIT {$limit}
        ";
        return $this->db->query($sql)->getResultArray();
    }

    // ─── DERNIÈRES SOUSCRIPTIONS RÉGIMES ─────────────────────────────────────

    public function getRecentUserRegimes(int $limit = 5): array
    {
        $sql = "
            SELECT ur.id, CONCAT(u.prenom, ' ', u.nom) AS utilisateur,
                   r.nom AS regime, ur.prix_paye, ur.date_debut, ur.date_fin,
                   s.nom AS statut
            FROM user_regime ur
            JOIN users u   ON u.id  = ur.user_id
            JOIN regime r  ON r.id  = ur.regime_id
            LEFT JOIN statut s ON s.id = ur.statut_id
            ORDER BY ur.id DESC
            LIMIT {$limit}
        ";
        return $this->db->query($sql)->getResultArray();
    }
}