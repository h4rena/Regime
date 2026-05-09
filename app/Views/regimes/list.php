<?php
$isGold = $isGold ?? false;
$regimes = $regimes ?? [];
$currentUser = session()->get('user') ?? [];
$displayName = trim(($currentUser['prenom'] ?? '') . ' ' . ($currentUser['nom'] ?? ''));
$displayName = $displayName !== '' ? $displayName : 'Utilisateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriPlan — Régimes</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
</head>
<body>
  <nav class="navbar">
    <div class="nav-inner">
      <div class="nav-logo"><span class="logo-dot"></span>NutriPlan</div>
      <ul class="nav-links">
        <li><a href="<?= base_url('/') ?>" class="nav-link">Accueil</a></li>
        <li><a href="<?= base_url('/regimes') ?>" class="nav-link active">Régimes</a></li>
        <li><a href="<?= base_url('/profil') ?>" class="nav-link">Mon profil</a></li>
        <li><a href="<?= base_url('/wallet') ?>" class="nav-link">Portefeuille</a></li>
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
      <div class="page-title-row">
        <div>
          <h1 class="page-h1">Tous les régimes</h1>
          <p class="page-h1-sub">Choisissez le programme adapté à vos objectifs</p>
        </div>
      </div>

      <?php if ($isGold): ?>
        <div class="gold-notice"><span>★</span><p><strong>Option Gold active</strong> — Vous bénéficiez de <strong>15% de remise</strong> sur tous les régimes ci-dessous.</p></div>
      <?php endif; ?>

      <div class="regimes-grid">
        <?php foreach ($regimes as $r): ?>
          <div class="regime-card-full">
            <div class="regime-card-top">
              <h2 class="regime-card-name"><?= esc((string) ($r['nom'] ?? '')) ?></h2>
              <p class="regime-card-meta"><?= esc((string) ((int) ($r['duree_jours'] ?? 0))) ?> jours · Variation <?= esc((string) ($r['variation_poids'] ?? '')) ?> kg</p>
              <p class="regime-card-desc">Description courte du régime.</p>
            </div>
            <div class="regime-card-footer">
              <div class="regime-card-price-block">
                <?php if ($isGold): ?>
                  <?php $old = number_format((float)$r['prix'],0,' ', ' '); $new = number_format(round((float)$r['prix']*0.85),0,' ', ' '); ?>
                  <div><span class="rcp-old"><?= esc($old) ?> Ar</span> <span class="rcp-price"><?= esc($new) ?> Ar</span></div>
                <?php else: ?>
                  <div><span class="rcp-price"><?= esc(number_format((float)$r['prix'],0,' ', ' ')) ?> Ar</span></div>
                <?php endif; ?>
              </div>
              <div class="regime-card-changes"><span class="rcc"><?= esc((string) ($r['variation_poids'] ?? '')) ?> kg</span></div>
              <a class="btn-primary-sm" href="<?= base_url('/regimes/' . $r['id']) ?>">Voir</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
</body>
</html>
