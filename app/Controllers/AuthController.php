<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SanteModel;
use App\Models\ObjectifModel;
use App\Models\GenreModel;

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
            return (new ObjectifModel())->findAll();
        } catch (\Throwable $e) {
            log_message('warning', 'ObjectifModel error: ' . $e->getMessage());
            return [];
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

    // ─────────────────────────────────────────
    //  LOGIN
    // ─────────────────────────────────────────

    /** Affiche le formulaire de connexion */
    public function loginForm()
    {
        // Déjà connecté → redirection
        if (session()->get('user')) {
            return redirect()->to('/profil');
        }

        return view('auth/login');
    }

    /** Traite la soumission du formulaire de connexion */
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

        if (! $user || ! password_verify($password, $user['password'])) {
            if ($this->wantsJson()) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'errors' => [
                        'email'    => 'Email ou mot de passe incorrect.',
                        'password' => 'Email ou mot de passe incorrect.',
                    ],
                ]);
            }

            return view('auth/login', [
                'errors' => [
                    'email'    => 'Email ou mot de passe incorrect.',
                    'password' => 'Email ou mot de passe incorrect.',
                ],
                'old' => [
                    'email' => $email,
                ],
            ]);
        }

        // Stocker uniquement les données non sensibles en session
        session()->set('user', [
            'id'             => $user['id'],
            'nom'            => $user['nom'],
            'prenom'         => $user['prenom'] ?? '',
            'email'          => $user['email'],
            'genre_id'       => $user['genre_id'] ?? null,
            'Date_naissance' => $user['Date_naissance'] ?? null,
            'role'           => $user['role'] ?? 'Utilisateur',
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

    /** Déconnexion */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    // ─────────────────────────────────────────
    //  INSCRIPTION — ÉTAPE 1 : infos utilisateur
    // ─────────────────────────────────────────

    /** Affiche le formulaire d'inscription étape 1 */
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

    /** Traite l'étape 1 : valide et stocke en session temporaire */
    public function register()
    {
        $genreId = $this->normalizeGenreId();

        $rules = [
            'nom'            => 'required|min_length[2]|max_length[100]',
            'prenom'         => 'required|min_length[2]|max_length[100]',
            'email'          => 'required|valid_email|is_unique[users.email]',
            'date_naissance' => 'required|valid_date[Y-m-d]',
            'password'       => 'required|min_length[8]',
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

        // Stocker temporairement les données étape 1 en session
        session()->set('inscription_step1', [
            'nom'      => $this->request->getPost('nom'),
            'prenom'   => $this->request->getPost('prenom'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'genre_id' => $genreId,
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

    // ─────────────────────────────────────────
    //  INSCRIPTION — ÉTAPE 2 : informations de santé
    // ─────────────────────────────────────────

    /** Affiche le formulaire d'inscription étape 2 */
    public function santeForm()
    {
        // Vérifier que l'étape 1 a bien été complétée
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

    /** Traite l'étape 2 : crée l'utilisateur + enregistre les données de santé */
    public function sante()
    {
        // Sécurité : étape 1 obligatoire
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
            'poids'  => [
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

        // Calcul de l'IMC
        $tailleM = $taille / 100;
        $imc     = round($poids / ($tailleM ** 2), 2);

        session()->set('inscription_step2', [
            'taille' => $taille,
            'poids'  => $poids,
            'imc'    => $imc,
        ]);

        return redirect()->to('/inscription/objectif');
    }

    /** Affiche l'étape objectif */
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

    /** Enregistre le compte après le choix d'objectif */
    public function objectif()
    {
        $step1 = session()->get('inscription_step1');
        $step2 = session()->get('inscription_step2');

        if (! $step1 || ! $step2) {
            return redirect()->to('/inscription')
                ->with('erreur', 'Veuillez compléter les étapes précédentes.');
        }

        if (! $this->validate(['id_objectif' => 'required|integer'])) {
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
            'nom'            => $step1['nom'],
            'prenom'         => $step1['prenom'],
            'email'          => $step1['email'],
            'password'       => $step1['password'],
            'genre_id'       => $step1['genre_id'],
            'Date_naissance' => $step1['Date_naissance'],
        ]);

        if (! $userId) {
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

        session()->set('user', [
            'id'             => $userId,
            'nom'            => $step1['nom'],
            'prenom'         => $step1['prenom'],
            'email'          => $step1['email'],
            'genre_id'       => $step1['genre_id'],
            'Date_naissance' => $step1['Date_naissance'],
            'role'           => 'Utilisateur',
            'is_gold'        => false,
        ]);

        session()->remove(['inscription_step1', 'inscription_step2']);

        return redirect()->to('/profil')->with('success', 'Succes enregistre. Votre compte a ete cree avec succes.');
    }

    /** Active l'option Gold avec un paiement unique débité du portefeuille. */
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
        $walletBalance = (float) ($user['wallet_balance'] ?? 0);

        if ($walletBalance < $goldPrice) {
            return redirect()->to('/profil')->with('erreur', 'Solde insuffisant pour activer Gold. Rechargez votre portefeuille puis réessayez.');
        }

        $newBalance = $walletBalance - $goldPrice;
        $db = \Config\Database::connect();

        $db->transStart();
        $userModel->update((int) $user['id'], [
            'is_gold' => 1,
            'wallet_balance' => $newBalance,
        ]);

        $db->table('wallet_transactions')->insert([
            'user_id' => (int) $user['id'],
            'montant' => $goldPrice,
            'type'    => 'debit',
        ]);
        $db->transComplete();

        $updatedSessionUser = $currentUser;
        $updatedSessionUser['is_gold'] = true;
        $updatedSessionUser['wallet_balance'] = $newBalance;
        session()->set('user', $updatedSessionUser);

        return redirect()->to('/profil')->with('success', 'Option Gold activée. Vous avez désormais 15% de remise sur tous les régimes.');
    }
}