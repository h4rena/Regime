<?php
$codes = $codes ?? [];
$error = $error ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Codes portefeuille — Admin</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
</head>
<body>
  <nav class="navbar">
    <div class="nav-inner">
      <div class="nav-logo"><span class="logo-dot"></span>NutriPlan Admin</div>
      <ul class="nav-links">
        <li><a href="<?= base_url('/profil') ?>" class="nav-link">Mon profil</a></li>
        <li><a href="<?= base_url('/dashboard') ?>" class="nav-link">Tableau de bord</a></li>
        <li><a href="<?= base_url('/admin/regimes') ?>" class="nav-link">Régimes</a></li>
        <li><a href="<?= base_url('/admin/activites') ?>" class="nav-link">Activités</a></li>
        <li><a href="<?= base_url('/admin/codes') ?>" class="nav-link active">Codes portefeuille</a></li>
        <li><a href="<?= base_url('/admin/parametres') ?>" class="nav-link">Paramètres</a></li>
      </ul>
      <div class="nav-actions">
        <span class="nav-user-name"><?= esc($displayName ?? 'Admin') ?></span>
        <a href="<?= base_url('/deconnexion') ?>" class="btn-nav-ghost">Déconnexion</a>
      </div>
    </div>
  </nav>

  <main class="page-main">
    <div class="container">
      <?php if ($error): ?>
        <div style="background:#FFF7E3;border:1px solid #F0C040;border-radius:8px;padding:16px;margin-bottom:16px;color:#A65B00;">
          <strong>⚠️ Erreur:</strong> <?= esc($error) ?>
        </div>
      <?php endif; ?>
      
      <div class="card">
        <div class="card-header-blue" style="justify-content:space-between;gap:12px;flex-wrap:wrap;">
          <h2 class="card-title-white">Codes portefeuille</h2>
          <a class="btn-primary-sm" href="<?= base_url('/admin/codes/create') ?>">+ Nouveau code</a>
        </div>
        <div class="card-body p0">
          <table class="data-table">
            <thead>
              <tr><th>Code</th><th>Montant</th><th>Statut</th><th>Utilisé par</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php foreach ($codes as $code): ?>
                <tr>
                  <td><strong><?= esc((string) ($code['code'] ?? '')) ?></strong></td>
                  <td><?= esc(number_format((float) ($code['montant'] ?? 0), 0, ',', ' ')) ?> Ar</td>
                  <td><?= ! empty($code['is_used']) ? 'Utilisé' : 'Disponible' ?></td>
                  <td><?= esc(trim((string) ($code['user_prenom'] ?? '') . ' ' . (string) ($code['user_nom'] ?? '')) ?: '—') ?></td>
                  <td><?= esc((string) ($code['used_at'] ?? '—')) ?></td>
                  <td class="td-actions">
                    <a class="btn-action-edit" href="<?= base_url('/admin/codes/edit/' . $code['id']) ?>">Modifier</a>
                    <a class="btn-action-del" href="<?= base_url('/admin/codes/delete/' . $code['id']) ?>" onclick="return confirm('Supprimer ce code ?')">Supprimer</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
