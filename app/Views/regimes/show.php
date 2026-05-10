<?php
$isGold = $isGold ?? false;
$regime = $regime ?? [];
$r = $regime;
$currentUser = session()->get('user') ?? [];
$isAdmin = (($currentUser['role'] ?? null) === 'Admin');
$displayName = trim(($currentUser['prenom'] ?? '') . ' ' . ($currentUser['nom'] ?? ''));
$displayName = $displayName !== '' ? $displayName : 'Utilisateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= esc($r['nom']) ?> — NutriPlan</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
</head>
<body>
  <?php
  $successMessage = session()->getFlashdata('success');
  $errorMessage = session()->getFlashdata('erreur');
  ?>
  <nav class="navbar">
    <div class="nav-inner">
      <div class="nav-logo"><span class="logo-dot"></span>NutriPlan</div>
      <ul class="nav-links">
        <li><a href="<?= base_url('/') ?>" class="nav-link">Accueil</a></li>
        <li><a href="<?= base_url('/regimes') ?>" class="nav-link active">Régimes</a></li>
        <li><a href="<?= base_url('/profil') ?>" class="nav-link">Mon profil</a></li>
        <li><a href="<?= base_url('/wallet') ?>" class="nav-link">Portefeuille</a></li>
        <?php if ($isAdmin): ?>
           <li><a href="<?= base_url('/admin') ?>" class="nav-link">Dashboard</a></li>
           <li><a href="<?= base_url('/admin/codes') ?>" class="nav-link">Codes portefeuille</a></li>
        <?php endif; ?>
      </ul>
      <div class="nav-actions">
        <span class="nav-user-name"><?= esc($displayName) ?></span>
        <?php if (! empty($currentUser['id'])): ?>
          <a href="<?= base_url('/deconnexion') ?>" class="btn-nav-ghost">Déconnexion</a>
        <?php else: ?>
          <a href="<?= base_url('/login') ?>" class="btn-nav-ghost">Connexion</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <main class="page-main">
    <div class="container">
      <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= esc((string) $successMessage) ?></div>
      <?php endif; ?>

      <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= esc((string) $errorMessage) ?></div>
      <?php endif; ?>

      <div class="page-title-row">
        <div>
          <h1 class="page-h1"><?= esc((string) ($r['nom'] ?? '')) ?></h1>
          <p class="page-h1-sub"><?= esc((string) ((int) ($r['duree_jours'] ?? 0))) ?> jours · Variation <?= esc((string) ($r['variation_poids'] ?? '')) ?> kg</p>
        </div>
      </div>

      <div class="regime-detail">
        <p>Description complète du régime, repas et conseils.</p>
        
        <!-- Composition du régime -->
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
          <h3 style="margin-bottom: 15px; color: #333;">Composition du régime</h3>
          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
            <div style="padding: 15px; background: white; border-radius: 6px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
              <div style="font-size: 28px; font-weight: bold; color: #d32f2f; margin-bottom: 5px;"><?= esc((string)($r['pourcentage_viande'] ?? '25')) ?>%</div>
              <div style="color: #666; font-size: 14px;">Viande</div>
            </div>
            <div style="padding: 15px; background: white; border-radius: 6px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
              <div style="font-size: 28px; font-weight: bold; color: #1976d2; margin-bottom: 5px;"><?= esc((string)($r['pourcentage_poisson'] ?? '40')) ?>%</div>
              <div style="color: #666; font-size: 14px;">Poisson</div>
            </div>
            <div style="padding: 15px; background: white; border-radius: 6px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
              <div style="font-size: 28px; font-weight: bold; color: #f57c00; margin-bottom: 5px;"><?= esc((string)($r['pourcentage_volaille'] ?? '35')) ?>%</div>
              <div style="color: #666; font-size: 14px;">Volaille</div>
            </div>
          </div>
          <div style="width: 100%; height: 30px; border-radius: 4px; overflow: hidden; display: flex;">
            <div style="width: <?= esc((string)($r['pourcentage_viande'] ?? '25')) ?>%; background: #d32f2f;"></div>
            <div style="width: <?= esc((string)($r['pourcentage_poisson'] ?? '40')) ?>%; background: #1976d2;"></div>
            <div style="width: <?= esc((string)($r['pourcentage_volaille'] ?? '35')) ?>%; background: #f57c00;"></div>
          </div>
        </div>

        <div class="regime-actions">
          <?php if ($isGold): ?>
            <div class="price"><span class="old"><?= esc(number_format((float)$r['prix'],0,' ', ' ')) ?> Ar</span> <strong><?= esc(number_format(round((float)$r['prix']*0.85),0,' ', ' ')) ?> Ar</strong></div>
          <?php else: ?>
            <div class="price"><strong><?= esc(number_format((float)$r['prix'],0,' ', ' ')) ?> Ar</strong></div>
          <?php endif; ?>
          <?php if (! empty($currentUser['id'])): ?>
            <form method="POST" action="<?= base_url('/regimes/souscrire/' . (int) ($r['id'] ?? 0)) ?>">
              <?= csrf_field() ?>
              <button class="btn-primary-sm" type="submit">Souscrire</button>
            </form>
          <?php else: ?>
            <a class="btn-primary-sm" href="<?= base_url('/login') ?>">Souscrire</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
