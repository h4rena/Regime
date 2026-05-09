<?php

namespace App\Controllers;

use App\Models\DashboardModel;

class DashbordController extends BaseController
{
    protected $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
    }

    // Seul l'utilisateur authentifié avec le rôle Admin peut accéder.
    private function checkAdmin()
    {
        $user = session()->get('user');

        if (! is_array($user) || ($user['role'] ?? null) !== 'Admin') {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }
        return null;
    }

    public function index()
    {
        try {
            $redirect = $this->checkAdmin();
            if ($redirect) return $redirect;

            $data = [
                'title'              => 'Tableau de Bord',
                'stats'              => $this->dashboardModel->getGlobalStats(),
                'users_per_month'    => $this->dashboardModel->getUsersPerMonth(),
                'regimes_popular'    => $this->dashboardModel->getPopularRegimes(),
                'objectifs_dist'     => $this->dashboardModel->getObjectifsDistribution(),
                'wallet_codes_stats' => $this->dashboardModel->getWalletCodesStats(),
                'gold_vs_normal'     => $this->dashboardModel->getGoldVsNormal(),
                'revenue_per_regime' => $this->dashboardModel->getRevenuePerRegime(),
                'activites_popular'  => $this->dashboardModel->getPopularActivites(),
                'imc_distribution'   => $this->dashboardModel->getImcDistribution(),
                'recent_users'       => $this->dashboardModel->getRecentUsers(5),
                'recent_regimes'     => $this->dashboardModel->getRecentUserRegimes(5),
            ];

            return view('admin/dashbord', $data);
        } catch (\Throwable $e) {
            log_message('error', 'DashbordController::index error: ' . $e->getMessage());
            
            return view('admin/dashbord', [
                'title'              => 'Tableau de Bord',
                'stats'              => [],
                'users_per_month'    => [],
                'regimes_popular'    => [],
                'objectifs_dist'     => [],
                'wallet_codes_stats' => [],
                'gold_vs_normal'     => [],
                'revenue_per_regime' => [],
                'activites_popular'  => [],
                'imc_distribution'   => [],
                'recent_users'       => [],
                'recent_regimes'     => [],
                'error'              => 'Erreur de base de données: ' . $e->getMessage(),
            ]);
        }
    }
}