<?php

namespace App\Controllers;

class BackofficeController extends BaseController
{
    private function monthLabel(string $monthKey): string
    {
        $map = [
            '01' => 'Jan',
            '02' => 'Fév',
            '03' => 'Mar',
            '04' => 'Avr',
            '05' => 'Mai',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Aoû',
            '09' => 'Sep',
            '10' => 'Oct',
            '11' => 'Nov',
            '12' => 'Déc',
        ];

        return $map[$monthKey] ?? $monthKey;
    }

    public function index()
    {
        try {
            $currentUser = session()->get('user') ?? [];
            $db = \Config\Database::connect();

            $stats = [
                'users'          => $db->table('users')->countAllResults(),
                'goldUsers'      => $db->table('users')->where('is_gold', 1)->countAllResults(),
                'regimes'        => $db->table('regime')->countAllResults(),
                'activities'     => $db->table('activite_sportive')->countAllResults(),
                'walletCodes'    => $db->table('wallet_codes')->countAllResults(),
                'walletAvailable'=> $db->table('wallet_codes')->where('is_used', 0)->countAllResults(),
                'walletBalance'  => (float) ($db->table('users')->selectSum('wallet_balance', 'total_balance')->get()->getRowArray()['total_balance'] ?? 0),
                'goldPrice'      => 50000,
                'discount'       => 15,
            ];

            $startDate = (new \DateTimeImmutable('first day of -5 months'))->format('Y-m-01 00:00:00');
            $signupRows = $db->table('users')
                ->select("DATE_FORMAT(created_at, '%Y-%m') AS month_key, COUNT(*) AS total", false)
                ->where('created_at >=', $startDate)
                ->groupBy('month_key')
                ->orderBy('month_key', 'ASC')
                ->get()
                ->getResultArray();

            $signupByMonth = [];
            foreach ($signupRows as $row) {
                $signupByMonth[$row['month_key']] = (int) $row['total'];
            }

            $signupSeries = [];
            for ($offset = 5; $offset >= 0; $offset--) {
                $date = (new \DateTimeImmutable('first day of this month'))->modify("-$offset month");
                $monthKey = $date->format('Y-m');
                $signupSeries[] = [
                    'label' => $this->monthLabel($date->format('m')),
                    'value' => $signupByMonth[$monthKey] ?? 0,
                ];
            }

            $recentUsers = $db->table('users')
                ->select('users.id, users.nom, users.prenom, users.email, users.wallet_balance, users.is_gold, users.created_at, role.nom AS role_nom', false)
                ->join('role', 'role.id = users.id_role', 'left')
                ->orderBy('users.id', 'DESC')
                ->limit(6)
                ->get()
                ->getResultArray();

            $regimes = $db->table('regime')
                ->select('id, nom, variation_poids, duree_jours, prix')
                ->orderBy('id', 'DESC')
                ->limit(6)
                ->get()
                ->getResultArray();

            $activities = $db->table('activite_sportive')
                ->select('id, nom, calories_par_heure, duree_recommandee_min')
                ->orderBy('id', 'DESC')
                ->limit(6)
                ->get()
                ->getResultArray();

            $walletCodes = $db->table('wallet_codes')
                ->select('wallet_codes.id, wallet_codes.code, wallet_codes.montant, wallet_codes.is_used, wallet_codes.used_at, users.nom AS user_nom, users.prenom AS user_prenom', false)
                ->join('users', 'users.id = wallet_codes.used_by', 'left')
                ->orderBy('wallet_codes.id', 'DESC')
                ->limit(6)
                ->get()
                ->getResultArray();

            $parameters = [];
            try {
                $parameters = $db->table('parametres')
                    ->orderBy('id', 'ASC')
                    ->get()
                    ->getResultArray();
            } catch (\Throwable $e) {
                $parameters = [
                    ['cle' => 'gold_price', 'libelle' => 'Prix Gold', 'valeur' => '50000', 'description' => 'Paiement unique'],
                    ['cle' => 'gold_discount', 'libelle' => 'Remise Gold', 'valeur' => '15', 'description' => 'Réduction sur les régimes'],
                ];
            }

            return view('admin/backoffice', [
                'currentUser'  => $currentUser,
                'stats'        => $stats,
                'signupSeries' => $signupSeries,
                'recentUsers'  => $recentUsers,
                'regimes'      => $regimes,
                'activities'   => $activities,
                'walletCodes'  => $walletCodes,
                'parameters'   => $parameters,
            ]);
        } catch (\Throwable $e) {
            // Log l'erreur
            log_message('error', 'BackofficeController error: ' . $e->getMessage());
            
            // Retourner une vue d'erreur simple sans dépendre de la BD
            return view('admin/backoffice', [
                'currentUser'  => session()->get('user') ?? [],
                'stats'        => ['users' => 0, 'goldUsers' => 0, 'regimes' => 0, 'activities' => 0, 'walletCodes' => 0, 'walletAvailable' => 0, 'walletBalance' => 0, 'goldPrice' => 50000, 'discount' => 15],
                'signupSeries' => [],
                'recentUsers'  => [],
                'regimes'      => [],
                'activities'   => [],
                'walletCodes'  => [],
                'parameters'   => [
                    ['cle' => 'gold_price', 'libelle' => 'Prix Gold', 'valeur' => '50000', 'description' => 'Paiement unique'],
                    ['cle' => 'gold_discount', 'libelle' => 'Remise Gold', 'valeur' => '15', 'description' => 'Réduction sur les régimes'],
                ],
                'error'        => 'Erreur de base de données: ' . $e->getMessage(),
            ]);
        }
    }
}