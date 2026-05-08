<?php
$user = $user ?? session()->get('user') ?? [];
$sante = $sante ?? [];
$isGold = ! empty($user['is_gold']);
$successMessage = session()->getFlashdata('success');
$errorMessage = session()->getFlashdata('erreur');
$goldPrice = 50000;
$displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''));
$displayName = $displayName !== '' ? $displayName : 'Ravo Andria';

$initials = strtoupper(substr((string) ($user['prenom'] ?? 'R'), 0, 1) . substr((string) ($user['nom'] ?? 'A'), 0, 1));
$email = (string) ($user['email'] ?? 'ravo@email.com');
$genre = (string) ($user['genre_nom'] ?? 'Femme');
$age = $user['age'] ?? null;
$taille = $sante['taille'] ?? null;
$poids = $sante['poids'] ?? null;
$imc = $user['imc'] ?? ($sante['imc'] ?? null);
$objectif = $sante['objectif_nom'] ?? "Atteindre l'IMC idéal";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriPlan - Mon profil</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
</head>
<body>

  <nav class="navbar">
    <div class="nav-inner">
      <div class="nav-logo"><span class="logo-dot"></span>NutriPlan</div>
      <ul class="nav-links">
        <li><a href="<?= base_url('/') ?>" class="nav-link">Accueil</a></li>
        <li><a href="<?= base_url('/regimes') ?>" class="nav-link">Régimes</a></li>
        <li><a href="<?= base_url('/profil') ?>" class="nav-link active">Mon profil</a></li>
        <li><a href="<?= base_url('/wallet') ?>" class="nav-link">Portefeuille</a></li>
      </ul>
      <div class="nav-actions">
        <span class="nav-user-name"><?= esc($displayName) ?></span>
        <a href="<?= base_url('/deconnexion') ?>" class="btn-nav-ghost">Déconnexion</a>
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

      <!-- PROFILE HEADER -->
      <div class="profile-header">
        <div class="profile-avatar"><?= esc($initials) ?></div>
        <div class="profile-info">
          <h1 class="profile-name"><?= esc($displayName) ?></h1>
          <p class="profile-meta"><?= esc($email) ?> · <?= esc($genre) ?> · <?= esc($age !== null ? (string) $age : '—') ?> ans</p>
          <?php if ($isGold): ?>
            <span class="badge badge-gold">★ Option Gold active</span>
          <?php else: ?>
            <span class="badge">Option Gold disponible</span>
          <?php endif; ?>
        </div>
        <a href="#" class="btn-outline-sm">Modifier le profil</a>
      </div>

      <?php if (! $isGold): ?>
        <div class="card mt-16">
          <div class="card-header-blue">
            <h2 class="card-title-white">Activer l'option Gold</h2>
          </div>
          <div class="card-body">
            <p class="form-help">Paiement unique de <?= esc(number_format($goldPrice, 0, '.', ' ')) ?> Ar. Gold donne 15% de remise sur tous les régimes.</p>
            <form method="POST" action="<?= base_url('/gold/activer') ?>">
              <?= csrf_field() ?>
              <button type="submit" class="btn-primary-sm">Activer Gold maintenant</button>
            </form>
          </div>
        </div>
      <?php endif; ?>

      <div class="profil-grid">

        <!-- LEFT: IMC CARD -->
        <div class="profil-left">
          <div class="card">
            <div class="card-header-blue">
              <h2 class="card-title-white">Mon IMC</h2>
            </div>
            <div class="card-body">
              <div class="imc-big-circle">
                <span class="imc-big-val"><?= esc($imc !== null ? number_format((float) $imc, 2, '.', '') : '—') ?></span>
                <span class="imc-big-unit">kg/m²</span>
              </div>
              <div class="imc-bar-full">
                <div class="imc-bar-track-full">
                  <div class="imc-bar-fill-full"></div>
                  <div class="imc-bar-pin-full"></div>
                </div>
                <div class="imc-zones">
                  <span>Maigreur<br/><small>&lt;18.5</small></span>
                  <span>Normal<br/><small>18.5–25</small></span>
                  <span>Surpoids<br/><small>25–30</small></span>
                  <span>Obésité<br/><small>&gt;30</small></span>
                </div>
              </div>
              <div class="imc-stats-row">
                <div class="imc-stat"><span class="imc-stat-val"><?= esc($taille !== null ? (string) $taille : '—') ?></span><span class="imc-stat-lbl">cm</span></div>
                <div class="imc-stat"><span class="imc-stat-val"><?= esc($poids !== null ? (string) $poids : '—') ?></span><span class="imc-stat-lbl">kg</span></div>
                <div class="imc-stat"><span class="imc-stat-val"><span class="badge badge-success">Normal</span></span></div>
              </div>
            </div>
          </div>

          <!-- OBJECTIF -->
          <div class="card mt-16">
            <div class="card-header-blue">
              <h2 class="card-title-white">Mon objectif</h2>
            </div>
            <div class="card-body">
              <div class="goal-chips-list">
                <div class="goal-chip-item">
                  <span class="goal-chip-icon">↑</span>
                  <span class="goal-chip-text">Augmenter son poids</span>
                </div>
                <div class="goal-chip-item selected">
                  <span class="goal-chip-icon sel">◎</span>
                  <span class="goal-chip-text"><?= esc($objectif) ?></span>
                  <span class="goal-current">Actuel</span>
                </div>
                <div class="goal-chip-item">
                  <span class="goal-chip-icon">↓</span>
                  <span class="goal-chip-text">Réduire son poids</span>
                </div>
              </div>
              <a href="<?= base_url('/regimes') ?>" class="btn-primary-full mt-12">Voir mes suggestions</a>
            </div>
          </div>
        </div>

        <!-- RIGHT: SUGGESTIONS -->
        <div class="profil-right">
          <div class="card">
            <div class="card-header-blue">
              <h2 class="card-title-white">Régimes suggérés</h2>
              <?php if ($isGold): ?>
                <span class="gold-pill">★ Gold −15%</span>
              <?php else: ?>
                <span class="gold-pill">Gold disponible</span>
              <?php endif; ?>
            </div>
            <div class="card-body">

              <div class="regime-item featured">
                <div class="regime-item-top">
                  <div>
                    <h3 class="regime-name">Régime Équilibré Marin</h3>
                    <p class="regime-meta">30 jours · −3 kg estimés</p>
                  </div>
                  <div class="regime-pricing">
                    <?php if ($isGold): ?>
                      <span class="regime-price-old">33 000 Ar</span>
                      <span class="regime-price">28 000 Ar</span>
                    <?php else: ?>
                      <span class="regime-price">33 000 Ar</span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="macro-bar-full">
                  <div class="macro-seg" style="width:40%;background:var(--blue-600)"></div>
                  <div class="macro-seg" style="width:35%;background:var(--blue-400)"></div>
                  <div class="macro-seg" style="width:25%;background:var(--blue-200)"></div>
                </div>
                <div class="macro-legend">
                  <span><span class="macro-dot" style="background:var(--blue-600)"></span>Poisson 40%</span>
                  <span><span class="macro-dot" style="background:var(--blue-400)"></span>Volaille 35%</span>
                  <span><span class="macro-dot" style="background:var(--blue-200)"></span>Viande 25%</span>
                </div>
                <a href="<?= base_url('/regimes') ?>" class="btn-primary-sm mt-10">Choisir ce régime</a>
              </div>

              <div class="regime-item">
                <div class="regime-item-top">
                  <div>
                    <h3 class="regime-name">Régime Léger Actif</h3>
                    <p class="regime-meta">14 jours · −1.5 kg estimés</p>
                  </div>
                  <div class="regime-pricing">
                    <?php if ($isGold): ?>
                      <span class="regime-price-old">21 000 Ar</span>
                      <span class="regime-price">18 000 Ar</span>
                    <?php else: ?>
                      <span class="regime-price">21 000 Ar</span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="macro-bar-full">
                  <div class="macro-seg" style="width:50%;background:var(--blue-400)"></div>
                  <div class="macro-seg" style="width:30%;background:var(--blue-200)"></div>
                  <div class="macro-seg" style="width:20%;background:var(--blue-600)"></div>
                </div>
                <div class="macro-legend">
                  <span><span class="macro-dot" style="background:var(--blue-400)"></span>Volaille 50%</span>
                  <span><span class="macro-dot" style="background:var(--blue-200)"></span>Poisson 30%</span>
                  <span><span class="macro-dot" style="background:var(--blue-600)"></span>Viande 20%</span>
                </div>
                <a href="<?= base_url('/regimes') ?>" class="btn-outline-sm mt-10">Choisir ce régime</a>
              </div>

              <div class="regime-item">
                <div class="regime-item-top">
                  <div>
                    <h3 class="regime-name">Régime Protéiné Force</h3>
                    <p class="regime-meta">21 jours · +2 kg masse</p>
                  </div>
                  <div class="regime-pricing">
                    <?php if ($isGold): ?>
                      <span class="regime-price-old">26 000 Ar</span>
                      <span class="regime-price">22 000 Ar</span>
                    <?php else: ?>
                      <span class="regime-price">26 000 Ar</span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="macro-bar-full">
                  <div class="macro-seg" style="width:50%;background:var(--blue-800)"></div>
                  <div class="macro-seg" style="width:30%;background:var(--blue-400)"></div>
                  <div class="macro-seg" style="width:20%;background:var(--blue-100)"></div>
                </div>
                <div class="macro-legend">
                  <span><span class="macro-dot" style="background:var(--blue-800)"></span>Viande 50%</span>
                  <span><span class="macro-dot" style="background:var(--blue-400)"></span>Volaille 30%</span>
                  <span><span class="macro-dot" style="background:var(--blue-100)"></span>Poisson 20%</span>
                </div>
                <a href="<?= base_url('/regimes') ?>" class="btn-outline-sm mt-10">Choisir ce régime</a>
              </div>

            </div>
          </div>

          <!-- EXPORT -->
          <div class="export-banner mt-16">
            <div class="export-icon-wrap">
              <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="12" x2="12" y2="18"/><polyline points="9 15 12 18 15 15"/></svg>
            </div>
            <div class="export-text-wrap">
              <p class="export-title">Exporter mon programme</p>
              <p class="export-sub">Régime complet + activités sportives en PDF</p>
            </div>
            <button class="btn-export">↓ PDF</button>
          </div>
        </div>

      </div>
    </div>
  </main>

</body>
</html>