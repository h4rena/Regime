<?php

namespace App\Controllers;

use App\Models\GenreModel;
use App\Models\SanteModel;
use App\Models\UserModel;

class Home extends BaseController
{
    public function index()
    {
        return view('auth/home');
    }

    public function profil()
    {
        $currentUser = session()->get('user');

        if (! $currentUser) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $user = $userModel->find((int) $currentUser['id']) ?? $currentUser;

        $genreNom = null;
        if (! empty($user['genre_id'])) {
            $genre = (new GenreModel())->find((int) $user['genre_id']);
            $genreNom = $genre['nom'] ?? null;
        }

        $age = null;
        if (! empty($user['Date_naissance'])) {
            try {
                $birthDate = new \DateTimeImmutable((string) $user['Date_naissance']);
                $today = new \DateTimeImmutable('today');
                $age = $birthDate->diff($today)->y;
            } catch (\Throwable) {
                $age = null;
            }
        }

        $sante = (new SanteModel())->getByUser((int) $user['id']);
        $imc = null;
        if ($sante && isset($sante['taille'], $sante['poids'])) {
            $tailleM = ((float) $sante['taille']) / 100;
            if ($tailleM > 0) {
                $imc = round(((float) $sante['poids']) / ($tailleM ** 2), 2);
            }
        }

        return view('auth/profil', [
            'user' => array_merge($user, [
                'genre_nom' => $genreNom,
                'age'       => $age,
                'imc'       => $imc,
            ]),
            'sante' => $sante,
        ]);
    }
}