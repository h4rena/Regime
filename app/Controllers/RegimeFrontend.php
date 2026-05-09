<?php

namespace App\Controllers;

use App\Models\RegimeModel;
use App\Models\SanteModel;

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
}
