<?php

namespace App\Controllers;

use App\Models\GenreModel;
use App\Models\SanteModel;
use App\Models\RegimeModel;
use App\Models\UserModel;

class Home extends BaseController
{
    private const PDF_PAGE_WIDTH = 595;
    private const PDF_PAGE_HEIGHT = 842;

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

    private function pdfWrapText(string $text, int $maxChars): array
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? $text);
        if ($text === '') {
            return [''];
        }

        $words = explode(' ', $text);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;

            if (strlen($candidate) <= $maxChars) {
                $current = $candidate;
                continue;
            }

            if ($current !== '') {
                $lines[] = $current;
            }

            if (strlen($word) <= $maxChars) {
                $current = $word;
                continue;
            }

            $chunks = str_split($word, $maxChars);
            $current = array_pop($chunks) ?: '';

            foreach ($chunks as $chunk) {
                $lines[] = $chunk;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    private function pdfBuildTextStream(array $lines, int $fontSize = 10, int $leading = 14, int $startX = 0, int $startY = 0): string
    {
        $stream = "BT\n/F1 {$fontSize} Tf\n{$leading} TL\n{$startX} {$startY} Td\n";

        foreach ($lines as $index => $line) {
            $escaped = $this->normalizePdfText((string) $line);
            $stream .= ($index === 0 ? '' : "T*\n") . '(' . $escaped . ") Tj\n";
        }

        $stream .= "ET\n";

        return $stream;
    }

    private function pdfAddCell(array &$lines, int $x, int $y, int $width, int $height, string $label, string $value): void
    {
        $labelLines = $this->pdfWrapText($label, 20);
        $valueLines = $this->pdfWrapText($value, 28);

        $lines[] = sprintf('0.95 0.97 1 rg %d %d %d %d re f', $x, $y, $width, $height);
        $lines[] = sprintf('0.82 0.88 0.96 RG %d %d %d %d re S', $x, $y, $width, $height);
        $lines[] = '0 0 0 rg';
        $lines[] = 'BT /F1 8 Tf 10 TL ' . ($x + 10) . ' ' . ($y + $height - 18) . ' Td (' . $this->normalizePdfText($labelLines[0] ?? $label) . ') Tj ET';

        $valueY = $y + $height - 34;
        foreach ($valueLines as $index => $line) {
            $lines[] = 'BT /F1 11 Tf 13 TL ' . ($x + 10) . ' ' . ($valueY - ($index * 13)) . ' Td (' . $this->normalizePdfText($line) . ') Tj ET';
        }
    }

    private function buildProfilePdf(array $profileData): string
    {
        $user = $profileData['user'] ?? [];
        $sante = $profileData['sante'] ?? [];
        $regimes = $profileData['regimes'] ?? [];
        $today = new \DateTimeImmutable('today');

        $displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''));
        $displayName = $displayName !== '' ? $displayName : 'Utilisateur';

        $pageCommands = [];

        $headerY = self::PDF_PAGE_HEIGHT - 126;
        $pageCommands[] = sprintf('0.06 0.17 0.32 rg 32 %d 531 88 re f', $headerY);
        $pageCommands[] = 'BT /F1 24 Tf 28 TL 52 ' . ($headerY + 52) . ' Td (NutriPlan) Tj ET';
        $pageCommands[] = 'BT /F1 15 Tf 18 TL 52 ' . ($headerY + 30) . ' Td (Fiche profil PDF) Tj ET';
        $pageCommands[] = 'BT /F1 10 Tf 12 TL 52 ' . ($headerY + 12) . ' Td (Document generate automatiquement pour le suivi du programme) Tj ET';
        $pageCommands[] = 'BT /F1 10 Tf 12 TL 450 ' . ($headerY + 52) . ' Td (' . $this->normalizePdfText($today->format('d/m/Y')) . ') Tj ET';
        $pageCommands[] = 'BT /F1 10 Tf 12 TL 450 ' . ($headerY + 34) . ' Td (Profil #' . (int) ($user['id'] ?? 0) . ') Tj ET';

        $pageCommands[] = 'BT /F1 16 Tf 20 TL 32 670 Td (Informations personnelles) Tj ET';
        $this->pdfAddCell($pageCommands, 32, 595, 245, 62, 'Nom complet', $displayName);
        $this->pdfAddCell($pageCommands, 288, 595, 245, 62, 'Email', (string) ($user['email'] ?? '—'));
        $this->pdfAddCell($pageCommands, 32, 522, 114, 56, 'Age', (string) ($user['age'] ?? '—') . ' ans');
        $this->pdfAddCell($pageCommands, 153, 522, 114, 56, 'Genre', (string) ($user['genre_nom'] ?? '—'));
        $this->pdfAddCell($pageCommands, 274, 522, 114, 56, 'IMC', (string) ($user['imc'] ?? '—'));
        $this->pdfAddCell($pageCommands, 395, 522, 138, 56, 'Statut Gold', ! empty($user['is_gold']) ? 'Actif' : 'Non');

        $pageCommands[] = 'BT /F1 16 Tf 20 TL 32 472 Td (Bilan de sante) Tj ET';
        $this->pdfAddCell($pageCommands, 32, 401, 114, 56, 'Poids', (string) ($sante['poids'] ?? '—') . ' kg');
        $this->pdfAddCell($pageCommands, 153, 401, 114, 56, 'Taille', (string) ($sante['taille'] ?? '—') . ' cm');
        $this->pdfAddCell($pageCommands, 274, 401, 114, 56, 'Wallet', number_format((float) ($user['wallet_balance'] ?? 0), 0, ',', ' ') . ' Ar');
        $this->pdfAddCell($pageCommands, 395, 401, 138, 56, 'Objectif', (string) ($sante['objectif_nom'] ?? 'Suivi du programme'));

        $pageCommands[] = 'BT /F1 16 Tf 20 TL 32 347 Td (Programmes recents) Tj ET';
        $pageCommands[] = '0.82 0.88 0.96 rg 32 315 499 24 re f';
        $pageCommands[] = '0.82 0.88 0.96 RG 32 315 499 24 re S';
        $pageCommands[] = 'BT /F1 9 Tf 11 TL 42 323 Td (Regime) Tj ET';
        $pageCommands[] = 'BT /F1 9 Tf 11 TL 330 323 Td (Duree) Tj ET';
        $pageCommands[] = 'BT /F1 9 Tf 11 TL 400 323 Td (Prix) Tj ET';
        $pageCommands[] = 'BT /F1 9 Tf 11 TL 470 323 Td (Ordre) Tj ET';

        $rowTop = 292;
        $rowIndex = 0;
        foreach ($regimes as $regime) {
            if ($rowTop < 96) {
                break;
            }

            $fill = $rowIndex % 2 === 0 ? '0.97 0.98 1 rg' : '1 1 1 rg';
            $pageCommands[] = sprintf('%s 32 %d 499 28 re f', $fill, $rowTop);
            $pageCommands[] = sprintf('0.88 0.92 0.97 RG 32 %d 499 28 re S', $rowTop);
            $pageCommands[] = 'BT /F1 9 Tf 11 TL 42 ' . ($rowTop + 16) . ' Td (' . $this->normalizePdfText((string) ($regime['nom'] ?? 'Régime')) . ') Tj ET';
            $pageCommands[] = 'BT /F1 9 Tf 11 TL 330 ' . ($rowTop + 16) . ' Td (' . $this->normalizePdfText((string) ($regime['duree_jours'] ?? '0') . ' jours') . ') Tj ET';
            $pageCommands[] = 'BT /F1 9 Tf 11 TL 400 ' . ($rowTop + 16) . ' Td (' . $this->normalizePdfText(number_format((float) ($regime['prix'] ?? 0), 0, ',', ' ') . ' Ar') . ') Tj ET';
            $pageCommands[] = 'BT /F1 9 Tf 11 TL 470 ' . ($rowTop + 16) . ' Td (#' . ($rowIndex + 1) . ') Tj ET';
            $rowTop -= 30;
            $rowIndex++;
        }

        if ($rowIndex === 0) {
            $pageCommands[] = 'BT /F1 10 Tf 12 TL 42 270 Td (Aucun regime recent disponible) Tj ET';
        }

        $pageCommands[] = 'BT /F1 8 Tf 10 TL 32 52 Td (NutriPlan - export profil) Tj ET';
        $pageCommands[] = 'BT /F1 8 Tf 10 TL 480 52 Td (Page 1/1) Tj ET';

        $content = implode("\n", $pageCommands) . "\n";

        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 " . self::PDF_PAGE_WIDTH . ' ' . self::PDF_PAGE_HEIGHT . "] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n";
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