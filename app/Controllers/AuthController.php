<?php

namespace App\Controllers;

use App\Models\GenreModel;
use App\Models\ObjectifModel;
use App\Models\SanteModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    private function defaultGenres(): array
    {
        return [
            ['id' => 1, 'nom' => 'Femme'],
            ['id' => 2, 'nom' => 'Homme'],
            ['id' => 3, 'nom' => 'Autre'],
        ];
    }

    private function wantsJson(): bool
    {
        return $this->request->isAJAX() || str_contains((string) $this->request->getHeaderLine('Accept'), 'application/json');
    }

    private function failValidation(string $view, array $viewData, array $errors)
    {
        if ($this->wantsJson()) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => 'error',
                'errors' => $errors,
            ]);
        }

        return view($view, array_merge($viewData, [
            'errors' => $errors,
            'old'    => $this->request->getPost(),
        ]));
    }

    private function getGenres(): array
    {
        try {
            $genres = (new GenreModel())->findAll();

            return ! empty($genres) ? $genres : $this->defaultGenres();
        } catch (\Throwable $e) {
            log_message('warning', 'GenreModel error: ' . $e->getMessage());

            return $this->defaultGenres();
        }
    }

    private function normalizeGenreId(): ?int
    {
        $genreId = $this->request->getPost('genre_id');

        if (is_array($genreId)) {
            $genreId = $genreId[0] ?? null;
        }

        return $genreId !== null && $genreId !== '' ? (int) $genreId : null;
    }

    private function getObjectives(): array
    {
        try {
            $objectifs = (new ObjectifModel())->findAll();

            if (! empty($objectifs)) {
                return $objectifs;
            }

            return [
                ['id' => 1, 'nom' => 'Perdre du poids'],
                ['id' => 2, 'nom' => 'Prendre du poids'],
                ['id' => 3, 'nom' => 'Maintenir le poids'],
                ['id' => 4, 'nom' => 'Améliorer ma santé'],
            ];
        } catch (\Throwable $e) {
            log_message('warning', 'ObjectifModel error: ' . $e->getMessage());

            return [
                ['id' => 1, 'nom' => 'Perdre du poids'],
                ['id' => 2, 'nom' => 'Prendre du poids'],
                ['id' => 3, 'nom' => 'Maintenir le poids'],
                ['id' => 4, 'nom' => 'Améliorer ma santé'],
            ];
        }
    }

    private function getIdealImcLabel(?array $step2 = null): array
    {
        $imcValue = null;

        if (! empty($step2['taille']) && ! empty($step2['poids'])) {
            $tailleM = ((float) $step2['taille']) / 100;

            if ($tailleM > 0) {
                $imcValue = round(((float) $step2['poids']) / ($tailleM ** 2), 2);
            }
        }

        return [
            'value'   => $imcValue !== null ? number_format($imcValue, 2, '.', '') : '—',
            'label'   => 'IMC ideal estime',
            'formula' => 'IMC = poids (kg) / taille² (m²)',
            'hint'    => 'Point de repere avant adaptation a votre objectif',
        ];
    }

    private function getGoldPrice(): int
    {
        return 50000;
    }

    private function getRoleIdByName(string $roleName): ?int
    {
        try {
            $role = \Config\Database::connect()
                ->table('role')
                ->select('id')
                ->where('nom', $roleName)
                ->get()
                ->getRowArray();

            return $role ? (int) $role['id'] : null;
        } catch (\Throwable $e) {
            log_message('warning', 'Role lookup error: ' . $e->getMessage());

            return null;
        }
    }

    private function getRoleNameById($roleId): string
    {
        if ($roleId === null || $roleId === '') {
            return 'Utilisateur';
        }

        try {
            $role = \Config\Database::connect()
                ->table('role')
                ->select('nom')
                ->where('id', (int) $roleId)
                ->get()
                ->getRowArray();

            return (string) ($role['nom'] ?? 'Utilisateur');
        } catch (\Throwable $e) {
            log_message('warning', 'Role name lookup error: ' . $e->getMessage());

            return 'Utilisateur';
        }
    }

    public function loginForm()
    {
        if (session()->get('user')) {
            return redirect()->to('/profil');
        }

        return view('auth/login');
    }

    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return $this->failValidation('auth/login', [], $this->validator->getErrors());
        }

        $model    = new UserModel();
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $user     = $model->where('email', $email)->first();

        if (! $user) {
            $errors = [
                'email' => 'Compte introuvable.',
            ];

            if ($this->wantsJson()) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'errors' => $errors,
                ]);
            }

            return view('auth/login', [
                'errors' => $errors,
                'old'    => ['email' => $email],
            ]);
        }

        if (! password_verify($password, $user['password'])) {
            $errors = [
                'password' => 'Mot de passe incorrect.',
            ];

            if ($this->wantsJson()) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'errors' => $errors,
                ]);
            }

            return view('auth/login', [
                'errors' => $errors,
                'old'    => ['email' => $email],
            ]);
        }

        session()->set('user', [
            'id'             => $user['id'],
            'nom'            => $user['nom'],
            'prenom'         => $user['prenom'] ?? '',
            'email'          => $user['email'],
            'genre_id'       => $user['genre_id'] ?? null,
            'Date_naissance' => $user['Date_naissance'] ?? null,
            'id_role'        => $user['id_role'] ?? null,
            'role'           => $this->getRoleNameById($user['id_role'] ?? null),
            'is_gold'        => (bool) ($user['is_gold'] ?? false),
        ]);

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'   => 'success',
                'redirect' => base_url('/profil'),
            ]);
        }

        return redirect()->to('/profil');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login');
    }

    public function registerForm()
    {
        if (session()->get('user')) {
            return redirect()->to('/profil');
        }

        return view('auth/inscription', [
            'genres' => $this->getGenres(),
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function register()
    {
        $genreId = $this->normalizeGenreId();

        $rules = [
            'nom'                   => 'required|min_length[2]|max_length[100]',
            'prenom'                => 'required|min_length[2]|max_length[100]',
            'email'                 => 'required|valid_email|is_unique[users.email]',
            'date_naissance'        => 'required|valid_date[Y-m-d]',
            'password'              => 'required|min_length[8]',
        ];

        $messages = [
            'email' => [
                'is_unique' => 'Cet email est déjà utilisé.',
            ],
            'password' => [
                'min_length' => 'Le mot de passe doit contenir au moins 8 caractères.',
            ],
        ];

        $isValid = $this->validate($rules, $messages);
        $errors  = $isValid ? [] : $this->validator->getErrors();

        if ($genreId === null) {
            $isValid = false;
            $errors['genre_id'] = 'Veuillez sélectionner un genre.';
        }

        if (! $isValid) {
            return $this->failValidation('auth/inscription', [
                'genres' => $this->getGenres(),
            ], $errors);
        }

        session()->set('inscription_step1', [
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'email'          => $this->request->getPost('email'),
            'password'       => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'genre_id'       => $genreId,
            'Date_naissance' => $this->request->getPost('date_naissance'),
        ]);

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'   => 'success',
                'redirect' => base_url('/inscription/sante'),
            ]);
        }

        return redirect()->to('/inscription/sante');
    }

    public function santeForm()
    {
        if (! session()->get('inscription_step1')) {
            return redirect()->to('/inscription')
                ->with('erreur', 'Veuillez d\'abord compléter vos informations personnelles.');
        }

        return view('auth/registresante', [
            'objectifs' => $this->getObjectives(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    public function sante()
    {
        $step1 = session()->get('inscription_step1');

        if (! $step1) {
            return redirect()->to('/inscription')
                ->with('erreur', 'Session expirée, recommencez l\'inscription.');
        }

        $rules = [
            'taille' => 'required|numeric|greater_than[100]|less_than[251]',
            'poids'  => 'required|numeric|greater_than[30]|less_than[301]',
        ];

        $messages = [
            'taille' => [
                'greater_than' => 'La taille doit être supérieure à 100 cm.',
                'less_than'    => 'La taille doit être inférieure à 250 cm.',
            ],
            'poids' => [
                'greater_than' => 'Le poids doit être supérieur à 30 kg.',
                'less_than'    => 'Le poids doit être inférieur à 300 kg.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return view('auth/registresante', [
                'errors'    => $this->validator->getErrors(),
                'objectifs' => $this->getObjectives(),
                'old'       => $this->request->getPost(),
            ]);
        }

        $taille = (float) $this->request->getPost('taille');
        $poids  = (float) $this->request->getPost('poids');

        $tailleM = $taille / 100;
        $imc     = round($poids / ($tailleM ** 2), 2);

        session()->set('inscription_step2', [
            'taille' => $taille,
            'poids'  => $poids,
            'imc'    => $imc,
        ]);

        return redirect()->to('/inscription/objectif');
    }

    public function objectifForm()
    {
        $step1 = session()->get('inscription_step1');
        $step2 = session()->get('inscription_step2');

        if (! $step1 || ! $step2) {
            return redirect()->to('/inscription')
                ->with('erreur', 'Veuillez compléter les étapes précédentes.');
        }

        return view('auth/objectif', [
            'objectifs' => $this->getObjectives(),
            'idealImc'  => $this->getIdealImcLabel($step2),
            'step2'     => $step2,
            'errors'    => [],
            'old'       => [],
        ]);
    }

    public function objectif()
    {
        $step1 = session()->get('inscription_step1');
        $step2 = session()->get('inscription_step2');

        if (! $step1 || ! $step2) {
            return redirect()->to('/inscription')
                ->with('erreur', 'Veuillez compléter les étapes précédentes.');
        }

        if (! $this->validate(['id_objectif' => 'required|integer'])) {
            if ($this->wantsJson()) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors(),
                ]);
            }

            return view('auth/objectif', [
                'objectifs' => $this->getObjectives(),
                'idealImc'  => $this->getIdealImcLabel($step2),
                'step2'     => $step2,
                'errors'    => $this->validator->getErrors(),
                'old'       => $this->request->getPost(),
            ]);
        }

        $userModel = new UserModel();
        $userId = $userModel->insert([
            'id_role'        => $this->getRoleIdByName('Utilisateur'),
            'nom'            => $step1['nom'],
            'prenom'         => $step1['prenom'],
            'email'          => $step1['email'],
            'password'       => $step1['password'],
            'genre_id'       => $step1['genre_id'],
            'Date_naissance' => $step1['Date_naissance'],
            'wallet_balance' => 0,
            'is_gold'        => 0,
        ]);

        if (! $userId) {
            if ($this->wantsJson()) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'errors' => [
                        'id_objectif' => 'Une erreur est survenue lors de la création du compte.',
                    ],
                ]);
            }

            return view('auth/objectif', [
                'objectifs' => $this->getObjectives(),
                'idealImc'  => $this->getIdealImcLabel($step2),
                'step2'     => $step2,
                'errors'    => [
                    'id_objectif' => 'Une erreur est survenue lors de la création du compte.',
                ],
                'old'       => [],
            ]);
        }

        $santeModel = new SanteModel();
        $santeModel->insert([
            'user_id'     => $userId,
            'taille'      => $step2['taille'],
            'poids'       => $step2['poids'],
            'imc'         => $step2['imc'],
            'id_objectif' => (int) $this->request->getPost('id_objectif'),
        ]);

        // Récupérer les données complètes de l'utilisateur depuis la BD
        $user = $userModel->find($userId);

        session()->set('user', [
            'id'             => $user['id'],
            'nom'            => $user['nom'],
            'prenom'         => $user['prenom'] ?? '',
            'email'          => $user['email'],
            'genre_id'       => $user['genre_id'] ?? null,
            'Date_naissance' => $user['Date_naissance'] ?? null,
            'id_role'        => $user['id_role'] ?? null,
            'role'           => $this->getRoleNameById($user['id_role'] ?? null),
            'is_gold'        => (bool) ($user['is_gold'] ?? false),
            'wallet_balance' => (float) ($user['wallet_balance'] ?? 0),
        ]);

        session()->remove(['inscription_step1', 'inscription_step2']);

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'   => 'success',
                'redirect' => base_url('/profil'),
            ]);
        }

        return redirect()->to('/profil')->with('success', 'Succes enregistre. Votre compte a ete cree avec succes.');
    }

    public function activateGold()
    {
        $currentUser = session()->get('user');

        if (! $currentUser || empty($currentUser['id'])) {
            return redirect()->to('/login')->with('erreur', 'Connectez-vous pour activer l\'option Gold.');
        }

        $userModel = new UserModel();
        $user = $userModel->find((int) $currentUser['id']);

        if (! $user) {
            return redirect()->to('/profil')->with('erreur', 'Compte introuvable.');
        }

        if (! empty($user['is_gold'])) {
            return redirect()->to('/profil')->with('success', 'Votre option Gold est déjà active.');
        }

        $goldPrice = $this->getGoldPrice();
        $walletModel = new \App\Models\WalletModel();
        $wallet = $walletModel->ensureWalletExists((int) $user['id']);
        $walletBalance = (float) ($wallet['montant'] ?? 0);

        if ($walletBalance < $goldPrice) {
            return redirect()->to('/profil')->with('erreur', 'Solde insuffisant pour activer Gold. Rechargez votre portefeuille puis réessayez.');
        }

        if (! $walletModel->debitMontantWallet($goldPrice, (int) $user['id'])) {
            return redirect()->to('/profil')->with('erreur', 'Impossible de débiter le portefeuille.');
        }

        $newBalance = $walletBalance - $goldPrice;

        $userModel->update((int) $user['id'], [
            'is_gold' => 1,
        ]);

        \Config\Database::connect()->table('wallet_transactions')->insert([
            'user_id' => (int) $user['id'],
            'montant' => $goldPrice,
            'type'    => 'debit',
        ]);

        $updatedSessionUser = $currentUser;
        $updatedSessionUser['is_gold'] = true;
        $updatedSessionUser['wallet_balance'] = $newBalance;
        session()->set('user', $updatedSessionUser);

        return redirect()->to('/profil')->with('success', 'Option Gold activée. Vous avez désormais 15% de remise sur tous les régimes.');
    }
}