<?php $isGold = $isGold ?? false; $regime = $regime ?? []; $r = $regime; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= esc($r['nom']) ?> — NutriPlan</title>
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
      </ul>
    </div>
  </nav>

  <main class="page-main">
    <div class="container">
      <div class="page-title-row">
        <div>
          <h1 class="page-h1"><?= esc($r['nom']) ?></h1>
          <p class="page-h1-sub"><?= esc((int)$r['duree_jours']) ?> jours · Variation <?= esc($r['variation_poids']) ?> kg</p>
        </div>
      </div>

      <div class="regime-detail">
        <p>Description complète du régime, repas et conseils.</p>
        <div class="regime-actions">
          <?php if ($isGold): ?>
            <div class="price"><span class="old"><?= esc(number_format((float)$r['prix'],0,' ', ' ')) ?> Ar</span> <strong><?= esc(number_format(round((float)$r['prix']*0.85),0,' ', ' ')) ?> Ar</strong></div>
          <?php else: ?>
            <div class="price"><strong><?= esc(number_format((float)$r['prix'],0,' ', ' ')) ?> Ar</strong></div>
          <?php endif; ?>
          <a class="btn-primary-sm" href="#">Souscrire</a>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
