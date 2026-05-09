<?php

namespace App\Controllers;

use App\Models\WalletCodeModel;

class WalletCodeController extends BaseController
{
    private function usersList(): array
    {
        try {
            return \Config\Database::connect()
                ->table('users')
                ->select('id, nom, prenom, email')
                ->orderBy('prenom', 'ASC')
                ->orderBy('nom', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'WalletCodeController::usersList error: ' . $e->getMessage());
            return [];
        }
    }

    public function index()
    {
        try {
            $db = \Config\Database::connect();

            $codes = $db->table('wallet_codes')
                ->select('wallet_codes.id, wallet_codes.code, wallet_codes.montant, wallet_codes.is_used, wallet_codes.used_at, wallet_codes.used_by, users.nom AS user_nom, users.prenom AS user_prenom, users.email AS user_email', false)
                ->join('users', 'users.id = wallet_codes.used_by', 'left')
                ->orderBy('wallet_codes.id', 'DESC')
                ->get()
                ->getResultArray();

            return view('admin/wallet_codes_list', [
                'codes' => $codes,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'WalletCodeController::index error: ' . $e->getMessage());
            return view('admin/wallet_codes_list', [
                'codes' => [],
                'error' => 'Erreur de base de données: ' . $e->getMessage(),
            ]);
        }
    }

    public function create()
    {
        return view('admin/wallet_code_form', [
            'mode'  => 'create',
            'code'  => [],
            'users' => $this->usersList(),
        ]);
    }

    public function store()
    {
        $rules = [
            'code'    => 'required|min_length[3]|max_length[20]',
            'montant' => 'required|decimal',
        ];

        if (! $this->validate($rules)) {
            return view('admin/wallet_code_form', [
                'mode'   => 'create',
                'code'   => $this->request->getPost(),
                'users'  => $this->usersList(),
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $model = new WalletCodeModel();
        $code = trim((string) $this->request->getPost('code'));

        if ($model->where('code', $code)->first()) {
            return view('admin/wallet_code_form', [
                'mode'   => 'create',
                'code'   => $this->request->getPost(),
                'users'  => $this->usersList(),
                'errors' => ['code' => 'Ce code portefeuille existe déjà.'],
            ]);
        }

        $isUsed = (bool) $this->request->getPost('is_used');
        $usedBy = $this->request->getPost('used_by');

        $model->insert([
            'code'     => $code,
            'montant'  => (float) $this->request->getPost('montant'),
            'is_used'  => $isUsed ? 1 : 0,
            'used_by'  => $usedBy !== '' && $usedBy !== null ? (int) $usedBy : null,
            'used_at'  => $isUsed ? date('Y-m-d H:i:s') : null,
        ]);

        return redirect()->to('/admin/codes')->with('success', 'Code portefeuille créé.');
    }

    public function edit($id)
    {
        $model = new WalletCodeModel();
        $code = $model->find((int) $id);

        if (! $code) {
            return redirect()->to('/admin/codes')->with('erreur', 'Code portefeuille introuvable.');
        }

        return view('admin/wallet_code_form', [
            'mode'  => 'edit',
            'code'  => $code,
            'users' => $this->usersList(),
        ]);
    }

    public function update($id)
    {
        $rules = [
            'code'    => 'required|min_length[3]|max_length[20]',
            'montant' => 'required|decimal',
        ];

        if (! $this->validate($rules)) {
            return view('admin/wallet_code_form', [
                'mode'   => 'edit',
                'code'   => array_merge($this->request->getPost(), ['id' => $id]),
                'users'  => $this->usersList(),
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $model = new WalletCodeModel();
        $code = trim((string) $this->request->getPost('code'));
        $existing = $model->where('code', $code)->first();

        if ($existing && (int) $existing['id'] !== (int) $id) {
            return view('admin/wallet_code_form', [
                'mode'   => 'edit',
                'code'   => array_merge($this->request->getPost(), ['id' => $id]),
                'users'  => $this->usersList(),
                'errors' => ['code' => 'Ce code portefeuille existe déjà.'],
            ]);
        }

        $isUsed = (bool) $this->request->getPost('is_used');
        $usedBy = $this->request->getPost('used_by');
        $existingById = $model->find((int) $id) ?: [];
        $usedAt = $existingById['used_at'] ?? null;

        if (! $isUsed) {
            $usedBy = null;
            $usedAt = null;
        } elseif (empty($usedAt)) {
            $usedAt = date('Y-m-d H:i:s');
        }

        $model->update((int) $id, [
            'code'    => $code,
            'montant' => (float) $this->request->getPost('montant'),
            'is_used' => $isUsed ? 1 : 0,
            'used_by' => $usedBy !== '' && $usedBy !== null ? (int) $usedBy : null,
            'used_at' => $usedAt,
        ]);

        return redirect()->to('/admin/codes')->with('success', 'Code portefeuille mis à jour.');
    }

    public function delete($id)
    {
        $model = new WalletCodeModel();
        $model->delete((int) $id);

        return redirect()->to('/admin/codes')->with('success', 'Code portefeuille supprimé.');
    }
}
