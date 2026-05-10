<?php

namespace App\Controllers;

use App\Models\GenreModel;
use App\Models\SanteModel;
use App\Models\RegimeModel;
use App\Models\UserModel;

class Home extends BaseController
{
    private function getProfileData(): array
    {
        $currentUser = session()->get('user');

        if (! $currentUser || empty($currentUser['id'])) {
            return [];
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

        $regimes = [];
        try {
            $regimes = (new RegimeModel())->orderBy('id', 'DESC')->findAll(5);
        } catch (\Throwable) {
            $regimes = [];
        }

        return [
            'user' => array_merge($user, [
                'genre_nom' => $genreNom,
                'age'       => $age,
                'imc'       => $imc,
            ]),
            'sante' => $sante,
            'regimes' => $regimes,
        ];
    }

    private function normalizePdfText(string $text): string
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);

        return $text;
    }

    private function buildProfilePdf(array $profileData): string
    {
        $user = $profileData['user'] ?? [];
        $sante = $profileData['sante'] ?? [];
        $regimes = $profileData['regimes'] ?? [];

        $displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''));
        $displayName = $displayName !== '' ? $displayName : 'Utilisateur';

        $lines = [
            'NutriPlan - Fiche profil',
            'Nom : ' . $displayName,
            'Email : ' . (string) ($user['email'] ?? '—'),
            'Genre : ' . (string) ($user['genre_nom'] ?? '—'),
            'Age : ' . (string) ($user['age'] ?? '—') . ' ans',
            'IMC : ' . ((string) ($user['imc'] ?? '—')),
            'Poids : ' . (string) ($sante['poids'] ?? '—') . ' kg',
            'Taille : ' . (string) ($sante['taille'] ?? '—') . ' cm',
            'Wallet : ' . number_format((float) ($user['wallet_balance'] ?? 0), 0, ',', ' ') . ' Ar',
            'Gold : ' . (! empty($user['is_gold']) ? 'Actif' : 'Non'),
            'Programmes recents',
        ];

        foreach ($regimes as $regime) {
            $lines[] = '- ' . (string) ($regime['nom'] ?? 'Régime') . ' | ' . (string) ($regime['duree_jours'] ?? '0') . ' jours | ' . number_format((float) ($regime['prix'] ?? 0), 0, ',', ' ') . ' Ar';
        }

        $contentLines = [];
        foreach ($lines as $line) {
            $contentLines[] = $this->normalizePdfText($line);
        }

        $content = "BT\n/F1 12 Tf\n14 TL\n50 800 Td\n";
        foreach ($contentLines as $index => $line) {
            $content .= ($index === 0 ? '' : 'T*\n') . '(' . $line . ") Tj\n";
        }
        $content .= "ET\n";

        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n";
        $objects[] = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $objects[] = "5 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n" . $content . "endstream\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefStart = strlen($pdf);
        $pdf .= "xref\n0 6\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= 5; $i++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$i]) . "\n";
        }
        $pdf .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n" . $xrefStart . "\n%%EOF";

        return $pdf;
    }

    public function index()
    {
        $currentUser = session()->get('user');
        $imc = null;
        $taille = null;
        $poids = null;

        if ($currentUser && ! empty($currentUser['id'])) {
            try {
                $sante = (new SanteModel())->getByUser((int) $currentUser['id']);
                if ($sante && isset($sante['taille'], $sante['poids'])) {
                    $taille = (float) $sante['taille'];
                    $poids  = (float) $sante['poids'];
                    $tailleM = $taille / 100;
                    if ($tailleM > 0) {
                        $imc = round($poids / ($tailleM ** 2), 2);
                    }
                }
            } catch (\Throwable $e) {
                // ignore and render default view
            }
        }

        return view('auth/home', [
            'currentUser' => $currentUser,
            'imc' => $imc,
            'taille' => $taille,
            'poids' => $poids,
        ]);
    }

    public function profil()
    {
        $profileData = $this->getProfileData();
        $currentUser = $profileData['user'] ?? null;

        if (! $currentUser) {
            return redirect()->to('/login');
        }

        return view('auth/profil', [
            'user'   => $profileData['user'] ?? [],
            'sante'  => $profileData['sante'] ?? [],
            'regimes'=> $profileData['regimes'] ?? [],
        ]);
    }

    public function profilPdf()
    {
        $profileData = $this->getProfileData();

        if (empty($profileData['user']['id'])) {
            return redirect()->to('/login');
        }

        $pdf = $this->buildProfilePdf($profileData);
        $fileName = 'nutriplan-profil-' . (int) $profileData['user']['id'] . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody($pdf);
    }

    public function editProfil()
    {
        $currentUser = session()->get('user');
        if (!$currentUser || empty($currentUser['id'])) {
            return redirect()->to('/login');
        }

        $profileData = $this->getProfileData();
        
        return view('auth/profil_edit', [
            'user'  => $profileData['user'] ?? [],
            'sante' => $profileData['sante'] ?? [],
        ]);
    }

    public function updateProfil()
    {
        $currentUser = session()->get('user');
        if (!$currentUser || empty($currentUser['id'])) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $sante = (new SanteModel())->getByUser((int) $currentUser['id']);

        $prenom = $this->request->getPost('prenom');
        $nom = $this->request->getPost('nom');
        $email = $this->request->getPost('email');
        $poids = $this->request->getPost('poids');
        $taille = $this->request->getPost('taille');

        $errors = [];

        if (empty($prenom)) {
            $errors['prenom'] = 'Prénom requis';
        }
        if (empty($nom)) {
            $errors['nom'] = 'Nom requis';
        }
        if (empty($email)) {
            $errors['email'] = 'Email requis';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide';
        }

        if (empty($poids) || !is_numeric($poids)) {
            $errors['poids'] = 'Poids invalide';
        }
        if (empty($taille) || !is_numeric($taille)) {
            $errors['taille'] = 'Taille invalide';
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        try {
            $userModel->update((int) $currentUser['id'], [
                'prenom' => $prenom,
                'nom'    => $nom,
                'email'  => $email,
            ]);

            if ($sante) {
                (new SanteModel())->update((int) $sante['id'], [
                    'poids' => (float) $poids,
                    'taille' => (int) $taille,
                ]);
            } else {
                (new SanteModel())->insert([
                    'user_id' => (int) $currentUser['id'],
                    'poids' => (float) $poids,
                    'taille' => (int) $taille,
                ]);
            }

            $updatedUser = $userModel->find((int) $currentUser['id']);
            session()->set('user', $updatedUser);

            return redirect()->to('/profil')->with('success', 'Profil mis à jour avec succès');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }
}