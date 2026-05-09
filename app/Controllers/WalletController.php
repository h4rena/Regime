<?php

namespace App\Controllers;

use App\Models\WalletModel;
use App\Models\WalletCodeModel;
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

        $walletModel = new WalletModel();

        $wallets = $walletModel->getWalletUser($userId);

        return view('auth/wallet', [
            'wallets' => $wallets
        ]);
    }

    public function ajouterMontant()
    {
        $user = session()->get('user');

        $userId = $user['id'] ?? null;

        if (empty($userId)) {

            if ($this->request->isAJAX()) {

                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Utilisateur non connecté.'
                ]);
            }

            return redirect()->to('/login');
        }

        $code = trim($this->request->getPost('code'));

        if (empty($code)) {

            $error = [
                'status'  => 'error',
                'message' => 'Veuillez entrer un code.'
            ];

            if ($this->request->isAJAX()) {
                return $this->response->setJSON($error);
            }

            return redirect()->to('/wallet')
                             ->with('error', $error['message']);
        }

        // Vérification et utilisation du code
        $walletCodeModel = new WalletCodeModel();

        $result = $walletCodeModel->updateWalletCode($code, $userId);

        // Récupération du nouveau solde
        $walletModel = new WalletModel();

        $wallet = $walletModel->getWalletUser($userId);

        $solde = $wallet['montant'] ?? 0;

        // AJAX => retour JSON
        if ($this->request->isAJAX()) {

            return $this->response->setJSON([
                'status'  => $result['status'],
                'message' => $result['message'],
                'solde'   => $solde
            ]);
        }

        // Requête normale
        if ($result['status'] === 'success') {

            return redirect()->to('/wallet')
                             ->with('success', $result['message']);
        }

        return redirect()->to('/wallet')
                         ->with('error', $result['message']);
    }
}
