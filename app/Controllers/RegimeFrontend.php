<?php

namespace App\Controllers;

use App\Models\RegimeModel;
use App\Models\SanteModel;
use App\Models\WalletModel;

class RegimeFrontend extends BaseController
{
    public function index()
    {
        $model = new RegimeModel();
        $regimes = $model->orderBy('id', 'ASC')->findAll();

        $currentUser = session()->get('user');
        $isGold = ! empty($currentUser['is_gold']);

        return view('regimes/list', [
            'regimes' => $regimes,
            'isGold'  => $isGold,
        ]);
    }

    public function show($id)
    {
        $model = new RegimeModel();
        $regime = $model->find((int) $id);

        if (! $regime) {
            return redirect()->to('/regimes')->with('erreur', 'Régime introuvable.');
        }

        $currentUser = session()->get('user');
        $isGold = ! empty($currentUser['is_gold']);

        return view('regimes/show', [
            'regime' => $regime,
            'isGold' => $isGold,
        ]);
    }

    public function subscribe($id)
    {
        $currentUser = session()->get('user');

        if (! $currentUser || empty($currentUser['id'])) {
            return redirect()->to('/login')->with('erreur', 'Connectez-vous pour souscrire à un régime.');
        }

        $regime = (new RegimeModel())->find((int) $id);
        if (! $regime) {
            return redirect()->to('/regimes')->with('erreur', 'Régime introuvable.');
        }

        $walletModel = new WalletModel();
        $wallet = $walletModel->ensureWalletExists((int) $currentUser['id']);
        $isGold = ! empty($currentUser['is_gold']);

        $basePrice = (float) ($regime['prix'] ?? 0);
        $finalPrice = $isGold ? round($basePrice * 0.85) : $basePrice;
        $balance = (float) ($wallet['montant'] ?? 0);

        if ($balance < $finalPrice) {
            return redirect()->to('/regimes/' . (int) $id)->with('erreur', 'Solde insuffisant pour souscrire à ce régime.');
        }

        if (! $walletModel->debitMontantWallet($finalPrice, (int) $currentUser['id'])) {
            return redirect()->to('/regimes/' . (int) $id)->with('erreur', 'Impossible de débiter le portefeuille.');
        }

        $db = \Config\Database::connect();
        $db->table('wallet_transactions')->insert([
            'user_id' => (int) $currentUser['id'],
            'montant' => $finalPrice,
            'type'    => 'debit',
        ]);

        $updatedUser = $currentUser;
        $updatedUser['wallet_balance'] = $balance - $finalPrice;
        session()->set('user', $updatedUser);

        return redirect()->to('/regimes/' . (int) $id)->with('success', 'Souscription validée.');
    }
}
