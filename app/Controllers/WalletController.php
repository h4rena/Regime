<?php

namespace App\Controllers;
use App\Models\WalletModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class WalletController extends BaseController
{
    public function afficheWalletUser()
    {
        $user = session()->get('user');
        $userId = $user['id'] ?? null;

        if (empty($userId)) {
            return redirect()->to('/login');
        }

        $wallets = (new WalletModel())->getWalletUser($userId);

        return view('auth/wallet', ['wallets' => $wallets]);
    }
}
